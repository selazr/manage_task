<?php
namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Crear usuaris
        $user1 = new User();
        $user1->setEmail('user1@example.com');
        $user1->setRoles(['ROLE_USER']);
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'password123'));
        $user1->setIsVerified(true);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('user2@example.com');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'password123'));
        $user2->setIsVerified(true);
        $manager->persist($user2);

        // Crear tasques
        $task1 = new Task();
        $task1->setTitle('Comprar pa');
        $task1->setContent('Anar a la fleca a comprar pa integral.');
        $task1->setPriority('Alta');
        $task1->setHours(1);
        $task1->setCreatedAt(new \DateTimeImmutable());
        $task1->setUser($user1); // Assignar la tasca a user1
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setTitle('Fer exercici');
        $task2->setContent('Anar al gimnàs durant una hora.');
        $task2->setPriority('Mitjana');
        $task2->setHours(1);
        $task2->setCreatedAt(new \DateTimeImmutable());
        $task2->setUser($user1); // Assignar la tasca a user1
        $manager->persist($task2);

        $task3 = new Task();
        $task3->setTitle('Llegir un llibre');
        $task3->setContent('Llegir el llibre "El Senyor dels Anells".');
        $task3->setPriority('Baixa');
        $task3->setHours(2);
        $task3->setCreatedAt(new \DateTimeImmutable());
        $task3->setUser($user2); // Assignar la tasca a user2
        $manager->persist($task3);

        // Guardar tot a la base de dades
        $manager->flush();
    }
}