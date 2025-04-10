import ky from 'ky';
import { FlashMessage } from 'components';

/*
Variable ky agissant comme un intercepteur
 - Récupère, si la réponse existe, le message d'erreur renvoyé
 - Appelle la route du refresh_token et relance la requête
*/
const api = ky.create({
    prefixUrl: '/api',
    headers: {
        'content-type': 'application/json'
    },
    hooks: {
        beforeError: [
            async error => {
                const response = error.response;
                if (response) {
                    const responseJSON = await error.response.json();
                    if (responseJSON) {
                        error.message = responseJSON.message;
                    } else {
                        error.message = 'Une erreur est survenue.';
                    }
                }
                return error;
            }
        ],
        afterResponse: [
			async (request, options, response) => {
                if(request.url.includes('token/refresh')) {
                    return response;
                }

				if (response.status === 401) {
                    const responseJSON = await response.json();
                    if (responseJSON.message.includes('JWT')) {
                        try {
                            await api('token/refresh');
                            return api(request);
                        } catch {
                            localStorage.setItem('flashMessage', JSON.stringify({
                                message: 'Une erreur est survenue. Vous avez été déconnecté.',
                                type: FlashMessage.Types.ERROR
                            }));
                            window.location.href = '/user/logout';
                        }
                    }
				}
			}
		]
    }
});

export default api;