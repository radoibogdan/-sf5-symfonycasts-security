<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Tag;
use App\Factory\AnswerFactory;
use App\Factory\QuestionFactory;
use App\Factory\QuestionTagFactory;
use App\Factory\TagFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        TagFactory::createMany(100);

        // Create 1 user with specific email
        UserFactory::createOne([
            'email' => 'radoi.office@gmail.com'
        ]);
        // Create 1 Admin with specific email + specific role
        UserFactory::createOne([
            'email' => 'admin@gmail.com',
            'roles' => ['ROLE_ADMIN'],
        ]);
        // Create 10 users random
        UserFactory::createMany(10);

        $questions = QuestionFactory::createMany(20, function () {
            return [
                'owner' => UserFactory::random()
            ];
        });

        QuestionTagFactory::createMany(100, function() {
            return [
                'tag' => TagFactory::random(),
                'question' => QuestionFactory::random(),
            ];
        });

        QuestionFactory::new()
            ->unpublished()
            ->many(5)
            ->create()
        ;

        AnswerFactory::createMany(100, function() use ($questions) {
            return [
                'question' => $questions[array_rand($questions)]
            ];
        });
        AnswerFactory::new(function() use ($questions) {
            return [
                'question' => $questions[array_rand($questions)]
            ];
        })->needsApproval()->many(20)->create();


        $manager->flush();
    }
}
