<?php

namespace App\Service\Users;

use App\DTO\Users\UserCreateDTO;
use App\DTO\Users\UserUpdateDTO;
use App\Entity\User;
use App\Repository\FriendRequestRepository;
use App\Repository\UserRepository;
use App\Validator\FilePicture\FilePicture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UsersService
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
        private ValidatorInterface $validator,
        private Filesystem $filesystem,
        private Security $security,

        #[Autowire('%kernel.project_dir%/public/uploads/profile-pictures')]
        private string $profilePictureDirectory,
        #[Autowire('%kernel.project_dir%/public')]
        private string $public,
    ) {
    }



    /**
     * Return user if exists.
     *
     * @throws HttpException if the targeted user doesn't exist
     */
    public function verifyUserExists(int $id): User
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Cet utilisateur n\'existe pas.');
        }

        return $user;
    }



    /**
     * @throws HttpException if the logged-in user is not the targeted one, unless he has admin permissions
     */
    public function verifySameUsers(User $targetedUser, User $loggedInUser): bool
    {
        if ($targetedUser !== $loggedInUser && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Vous n\'êtes pas autorisé à effectuer cette action.');
        }

        return true;
    }



    /**
     * @throws HttpException if user1 and user2 are the same user
     */
    public function verifyNotSameUsers(User $user1, User $user2): bool
    {
        if ($user1 === $user2) {
            throw new HttpException(Response::HTTP_CONFLICT, 'Vous ne pouvez pas effectuer cette action sur vous-même.');
        }

        return true;
    }



    /**
     * Register a user based on UserCreateDTO data.
     *
     * @return User the user registered
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
     * Get user by ID.
     *
     * @throws HttpException if the user doesn't exist (from verifyUserExists())
     */
    public function get(int $id): User
    {
        return $this->verifyUserExists($id);
    }



    /**
     * Update targeted user by id based on UserUpdateDTO data.
     *
     * @throws HttpException if the targeted user doesn't exist (from verifyUserExists()).
     *                       if the logged-in user is not the targeted one, unless he has admin permissions (from verifySameUsers()).
     */
    public function update(int $id, UserUpdateDTO $userDTO): void
    {
        /** @var User $user */
        $loggedInUser = $this->security->getUser();

        $user = $this->get($id);
        $this->verifySameUsers($user, $loggedInUser);

        $user
            ->setEmail($userDTO->email)
            ->setName($userDTO->name)
            ->setSurname($userDTO->surname)
            ->setCountry($userDTO->country)
            ->setWebsite($userDTO->website)
            ->setGithub($userDTO->github)
            ->setLinkedin($userDTO->linkedin);

        $violations = $this->validator->validate($user);
        if (count($violations) > 0) {
            throw new ValidationFailedException($user, $violations); // Validation on entity to handle uniqueEntity constraints
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return;
    }



    /**
     * Upload profile picture and updates targeted user by id.
     *
     * This function handles profile picture uploads by deleting the old one if it exists, saving the new one and updating
     * profile picture path from $user data.
     *
     * @param UploadedFile $file the new profile picture
     *
     * @throws HttpException if the targeted user doesn't exist (from verifyUserExists()).
     *                       if the logged-in user is not the targeted one, unless he has admin permissions (from verifySameUsers()).
     *                       if the uploaded file fails the validation constraints.
     */
    public function uploadProfilePicture(int $id, UploadedFile $file): User
    {
        $loggedInUser = $this->security->getUser();

        $user = $this->get($id);
        $this->verifySameUsers($user, $loggedInUser);

        $violations = $this->validator->validate($file, new FilePicture());
        if (count($violations) > 0) {
            throw new ValidationFailedException($file, $violations);
        }

        $oldProfilePicture = $user->getProfilePicture();
        $oldFilePath = $this->public.'/'.$oldProfilePicture;
        if ($this->filesystem->exists($oldFilePath) && $oldFilePath !== $this->public.User::getDefaultProfilePicturePath()) {
            $this->filesystem->remove($oldFilePath);
        }

        $fileName = $id.'.'.$file->guessExtension();

        $file->move($this->profilePictureDirectory, $fileName);
        $user->setProfilePicture(User::getProfilePicturesPath().'/'.$fileName);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }



    /**
     * Delete targeted user by id and his data, linked files included.
     *
     * @throws HttpException if the targeted user doesn't exist (from get()).
     *                       if the logged-in user is not the targeted one, unless he has admin permissions (from verifySameUsers()).
     */
    public function delete(int $id): void
    {
        $loggedInUser = $this->security->getUser();

        $user = $this->get($id);
        $this->verifySameUsers($user, $loggedInUser);

        if ($user->getProfilePicture() !== User::getDefaultProfilePicturePath()) {
            $filePath = $this->public.User::getProfilePicturesPath().'/'.$id;
            $file = new File(glob($filePath.'.*')[0]);
            if ($this->filesystem->exists($file)) {
                $this->filesystem->remove($file);
            }
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return;
    }
}
