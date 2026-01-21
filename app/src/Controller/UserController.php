<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/create-user', name: 'app_create_user')]
    public function createUser(
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $user->setEmail('admin@gmail.com');
        $user->setRoles(['ROLE_USER']);

        // Hash the plain password
        $plainPassword = 'qwerty';
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plainPassword
        );
        $user->setPassword($hashedPassword);

        // Persist and flush the user to the database
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('User created successfully!');
    }
}