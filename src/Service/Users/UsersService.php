<?php 

namespace App\Service\Users;

use App\Entity\User;
use App\DTO\Users\UserCreateDTO;
use App\DTO\Users\UserUpdateDTO;
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



    public function verifyUserExists(int $id) : User {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Cet utilisateur n\'existe pas.');
        }

        return $user;
    }



    public function verifySameUsers(User $targetedUser, User $loggedInUser) : void {
        if ($targetedUser !== $loggedInUser && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new HttpException(Response::HTTP_FORBIDDEN, "Vous n'êtes pas autorisé à effectuer cette action.");
        }
    }



    public function verifyNotSameUsers(User $user1, User $user2) : void {
        if ($user1 === $user2) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "Vous ne pouvez pas effectuer cette action sur vous-même.");
        }
    }



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



    public function get(int $id) : User {
        return $this->verifyUserExists($id);
    }



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



    public function uploadProfilePicture(int $id, UploadedFile $file) {
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
            $oldFilePath = $this->public . "/" . $oldProfilePicture;
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



    public function delete(int $id) {
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

    

    public function sendFriendRequest(int $id) {
        /** @var User $userSender */
        $userSender = $this->security->getUser(); 

        $userReceiver = $this->verifyUserExists($id);
        $this->verifyNotSameUsers($userSender, $userReceiver);

        $userSender->sendFriendRequest($userReceiver);

        $this->entityManager->persist($userSender);
        $this->entityManager->flush();
    }



    public function cancelFriendRequest(int $id) {
        /** @var User $userSender */
        $userSender = $this->security->getUser(); 

        $userReceiver = $this->verifyUserExists($id);
        $this->verifyNotSameUsers($userSender, $userReceiver);

        $userSender->cancelFriendRequest($userReceiver);

        $this->entityManager->persist($userSender);
        $this->entityManager->flush();
    }
}