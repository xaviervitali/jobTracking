<?php

namespace App\DataFixtures;

use App\Constants\ActionStatus;
use App\Entity\Action;
use App\Entity\User;
use App\Entity\Job;
use App\Entity\JobTracking;
use App\Entity\Note;
use DateTime;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use App\Enums\PostitColors;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends AbstractFixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function loadData(ObjectManager $manager): void
    {
        $plaintextPassword = 'abcd';
        $actions = ActionStatus::getActions();
        $this->createMany(User::class, 10, function ($user) use ($plaintextPassword) {

            $user
                ->setEmail($this->faker->email())
                ->setFirstname($this->faker->firstName())
                ->setLastname($this->faker->lastName());
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );

            $user->setPassword($hashedPassword);
        });

        $this->createMany(Job::class, 500, function ($job) {
            $createdAt =  $this->faker->dateTimeBetween('-1 year', '-3 days');
            $jobCreatedAt = DateTimeImmutable::createFromMutable($createdAt);

            $job->setTitle($this->faker->catchPhrase())
                ->setRecruiter($this->faker->company())
                ->setOfferDescription($this->faker->paragraphs(rand(1, 10), true))
                ->setCreatedAt($jobCreatedAt)
                ->setSource($this->faker->words(3, true))
                ->setUser($this->getRandomreference(User::class))
            ;

            if (rand(0, 10) > 7) {
                $job->setCoverLetter($this->faker->paragraphs(rand(1, 10), true));
            }
        });

        $i = 0;
        foreach ($actions as   $action => $value) {
            $newAction = new Action();
            $newAction
                ->setName($action)
                ->setSetClosed($value);
                
            $manager->persist($newAction);
            $this->addReference(Action::class . '_' . $i, $newAction);
            $i++;
        }
        $manager->flush();

        $this->createMany(JobTracking::class, 50, function ($jobTracking) {
            $job = $this->getRandomreference(Job::class);


            $user =  $job->getUser();

            $jobCreatedAt = DateTime::createFromImmutable($job->getCreatedAt());
            $createdAt =  DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween($jobCreatedAt));



            $action =  $this->getRandomreference(Action::class);
            $jobTracking
                ->setJob($job)
                ->setAction($action)
                ->setCreatedAt($createdAt)
                ->setUser($user);
        });


        $this->createMany(Note::class, 50, function ($note) {
            $job = $this->getRandomreference(Job::class);
            
            $user =  $job->getUser();
            $jobCreatedAt = DateTime::createFromImmutable($job->getCreatedAt());
            $createdAt =  DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween($jobCreatedAt));
            $color =$this->faker->randomElement(PostitColors::getColors());

            $note
                ->setCreatedAt($createdAt)
                ->setContent($this->faker->paragraphs(rand(1, 2), true))
                ->setUser($user)
                ->setColor($color)
                ->setJob($job); 
        });
    }
}
