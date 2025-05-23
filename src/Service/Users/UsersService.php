<?php 

namespace App\Service\Users;

use App\Entity\User;
use App\DTO\Users\UserCreateDTO;
use App\DTO\Users\UserUpdateDTO;
use App\Entity\FriendRequest;
use App\Repository\FriendRequestRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Validator\FilePicture\FilePicture;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersService
{
    public function __construct(
        private UserRepository $userRepository,
        private FriendRequestRepository $friendRequestRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
        private ValidatorInterface $validator,
        private Filesystem $filesystem,
        private Security $security,

        #[Autowire('%kernel.project_dir%/public/uploads/profile-pictures')] 
        private string $profilePictureDirectory,
        #[Autowire('%kernel.project_dir%/public')] 
        private string $public,
    ) {}



    /**
     * Returns user if exists.
     *
     * @param int $id
     *
     * @throws HttpException if the targeted user doesn't exist.
     * 
     * @return User
     */
    public function verifyUserExists(int $id) : User {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Cet utilisateur n\'existe pas.');
        }

        return $user;
    }



    /**
     * @param User $targetedUser
     * @param User $loggedInUser
     *
     * @throws HttpException if the logged-in user is not the targeted one, unless he has admin permissions.
     * 
     * @return bool
     */
    public function verifySameUsers(User $targetedUser, User $loggedInUser) : bool {
        if ($targetedUser !== $loggedInUser && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Vous n\'êtes pas autorisé à effectuer cette action.');
        }

        return true;
    }



    /**
     * @param User $user1
     * @param User $user2
     *
     * @throws HttpException if user1 and user2 are the same user.
     * 
     * @return bool
     */
    public function verifyNotSameUsers(User $user1, User $user2) : bool {
        if ($user1 === $user2) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Vous ne pouvez pas effectuer cette action sur vous-même.');
        }

        return true;
    }



    /**
     * Returns a friend request if exists.
     *
     * @param User $userSender
     * @param User $userReceiver
     *
     * @throws HttpException if no friend request exists between each others.
     * 
     * @return FriendRequest [sender - receiver]
     */
    public function verifyFriendRequestExists(User $userSender, User $userReceiver) : FriendRequest {
        $friendRequest = $this->friendRequestRepository->findOneBy([
            'userSender' => $userSender,
            'userReceiver' => $userReceiver
        ]);
        if (!$friendRequest) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Il n\'y a pas de demande d\'ajout en attente avec cet utilisateur.');
        }

        return $friendRequest;
    }
 


    /**
     * @param User $userSender
     * @param User $userReceiver
     *
     * @throws HttpException if a friend request exists already and is pending.
     * 
     * @return bool
     */
    public function verifyFriendRequestNotPending(User $userSender, User $userReceiver) : bool {
        if ($this->friendRequestRepository->relationExists($userSender, $userReceiver)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Une demande d\'ajout est déjà en attente.');
        }

        return true;
    }



    /**
     * Register a user based on UserCreateDTO data.
     * 
     * @param UserCreateDTO $userDTO
     * 
     * @return User the user registered.
     */
    public function register(UserCreateDTO $userDTO): User
    {
        $user = new User();
        $user
            ->setUsername($userDTO->username)
            ->setEmail($userDTO->email)
            ->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $userDTO->plainPassword
                )
            )
            ->setName($userDTO->name)
            ->setSurname($userDTO->surname)
            ->setCountry($userDTO->country);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }



    /**
     * Gets user by ID.
     *
     * @param int $id
     *
     * @throws HttpException if the user doesn't exist (from verifyUserExists()).
     *
     * @return User
     */
    public function get(int $id) : User {
        return $this->verifyUserExists($id);
    }



    /**
     * Updates targeted user by id based on UserUpdateDTO data.
     *
     * @param int $id
     *
     * @throws HttpException if the targeted user doesn't exist (from verifyUserExists()).
     *                       if the logged-in user is not the targeted one, unless he has admin permissions (from verifySameUsers()).
     */
    public function update(int $id, UserUpdateDTO $userDTO): void
    {
        /** @var User $user */
        $loggedInUser = $this->security->getUser();
        
        $user = $this->verifyUserExists($id);
        $this->verifySameUsers($user, $loggedInUser);

        $user
            ->setEmail($userDTO->email)
            ->setName($userDTO->name)
            ->setSurname($userDTO->surname)
            ->setCountry($userDTO->country)
            ->setWebsite($userDTO->website)
            ->setGithub($userDTO->github)
            ->setLinkedin($userDTO->linkedin);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }



    /**
     * Uploads profile picture and updates targeted user by id.
     *
     * This function handles profile picture uploads by deleting the old one if it exists, saving the new one and updating
     * profile picture path from $user data.
     * @param int $id
     * @param UploadedFile $file the new profile picture.
     *
     * @throws HttpException if the targeted user doesn't exist (from verifyUserExists()).
     *                       if the logged-in user is not the targeted one, unless he has admin permissions (from verifySameUsers()).
     *                       if the uploaded file fails the validation constraints.
     * 
     * @return User
     */
    public function uploadProfilePicture(int $id, UploadedFile $file) : User {
        $loggedInUser = $this->security->getUser();

        $user = $this->verifyUserExists($id);
        $this->verifySameUsers($user, $loggedInUser);

        $violations = $this->validator->validate($file, new FilePicture());
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            throw new HttpException(Response::HTTP_BAD_REQUEST, implode(' ', $errors));
        }

        $oldProfilePicture = $user->getProfilePicture();
        if ($oldProfilePicture) {
            $oldFilePath = $this->public . '/' . $oldProfilePicture;
            if ($this->filesystem->exists($oldFilePath) && $oldFilePath !== User::getDefaultProfilePicturePath()) { 
                $this->filesystem->remove($oldFilePath);
            }
        }

        $fileName = $id . '.' . $file->guessExtension();

        $file->move($this->profilePictureDirectory, $fileName);
        $user->setProfilePicture(User::getProfilePicturesPath() . '/' . $fileName);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }



    /**
     * Delete targeted user by id and his data, linked files included.
     *
     * @param int $id
     *
     * @throws HttpException if the targeted user doesn't exist (from verifyUserExists()).
     *                       if the logged-in user is not the targeted one, unless he has admin permissions (from verifySameUsers()).
     */
    public function delete(int $id) : void {
        $loggedInUser = $this->security->getUser();

        $user = $this->verifyUserExists($id);
        $this->verifySameUsers($user, $loggedInUser);

        if ($user->getProfilePicture() !== User::getDefaultProfilePicturePath()) {
            $filePath = $this->public . User::getProfilePicturesPath() . '/' . $id;
            $file = new File(glob($filePath . '.*')[0]);
            if ($this->filesystem->exists($file)) {
                $this->filesystem->remove($file);
            }
        }
        
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    

    /**
     * Send a friend request to another user.
     *
     * @param int $id the id of the receiver.
     *
     * @throws HttpException if the targeted user doesn't exist (from verifyUserExists()).
     *                       if the logged-in user sends a request to himself (from verifyNotSameUsers()).
     */
    public function sendFriendRequest(int $id) : void {
        /** @var User $userSender */
        $userSender = $this->security->getUser(); 

        $userReceiver = $this->verifyUserExists($id);
        $this->verifyNotSameUsers($userSender, $userReceiver);

        if($this->verifyFriendRequestNotPending($userSender, $userReceiver)) {
            $friendRequest = new FriendRequest($userSender, $userReceiver);
            
            $this->entityManager->persist($friendRequest);
            $this->entityManager->flush();
        }
    }


    /**
     * Cancel a friend request previously sent.
     *
     * @param int $id the id of the receiver.
     *
     * @throws HttpException if the targeted user doesn't exist (from verifyUserExists()).
     *                       if the logged-in user cancels a request to himself (from verifyNotSameUsers()).
     */
    public function cancelFriendRequest(int $id) {
        /** @var User $userSender */
        $userSender = $this->security->getUser(); 

        $userReceiver = $this->verifyUserExists($id);
        $this->verifyNotSameUsers($userSender, $userReceiver);

        $friendRequest = $this->verifyFriendRequestExists($userSender, $userReceiver);
        if($friendRequest) {
            $this->entityManager->remove($friendRequest);
            $this->entityManager->flush();
        }
    }
}