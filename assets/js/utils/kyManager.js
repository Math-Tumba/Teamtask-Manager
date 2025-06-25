import ky from 'ky';
import { FlashMessage } from 'components/flashMessage';

/**
 * Ky variable acting as an interceptor.
 * 
 * Hooks
 * - beforeError : Retrieves, if exists, the message contained in the response's error.
 * - afterResponse : If the response corresponds to a 401 error due to JWT expired BEARER token, it calls the
 *                   refresh token route so a newly JWT can be stored and used without disconnecting the user.
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