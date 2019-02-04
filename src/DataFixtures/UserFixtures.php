<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;
use App\Entity\User;

class UserFixtures extends Fixture
{
	private $passwordEncoder;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->passwordEncoder = $passwordEncoder;
	}

    public function load(ObjectManager $manager)
    {
		$faker = Faker\Factory::create('fr_FR');

		for ($i = 0; $i < 5; $i++) {
			$user = new User();
			$user->setUsername($faker->userName);
			$user->setPassword($this->passwordEncoder->encodePassword($user, 'bbbbb'));
			$user->setEmail(sprintf('usernb%d@test.com', $i));
			$user->setRoles(['ROLE_USER']);
			$user->setCreatedAt(new \Datetime);
			$user->setFirstName($faker->firstName);
			$user->setLastName($faker->lastName);

			$manager->persist($user);
		}
		for ($i = 5; $i < 10; $i++) {
			$user = new User();
			$user->setUsername($faker->userName);
			$user->setPassword($this->passwordEncoder->encodePassword($user, 'bbbbb'));
			$user->setEmail(sprintf('usernb%d@test.com', $i));
			$user->setRoles(['ROLE_MEMBRE']);
			$user->setCreatedAt(new \Datetime);
			$user->setFirstName($faker->firstName);
			$user->setLastName($faker->lastName);

			$manager->persist($user);
		}
		for ($i = 10; $i < 15; $i++) {
			$user = new User();
			$user->setUsername($faker->userName);
			$user->setPassword($this->passwordEncoder->encodePassword($user, 'bbbbb'));
			$user->setEmail(sprintf('usernb%d@test.com', $i));
			$user->setRoles(['ROLE_MEMBRE_POMPIER']);
			$user->setCreatedAt(new \Datetime);
			$user->setFirstName($faker->firstName);
			$user->setLastName($faker->lastName);

			$manager->persist($user);
		}
		
		$user = new User();
		$user->setUsername('Tengu');
		$user->setPassword($this->passwordEncoder->encodePassword($user, 'bbbbb'));
		$user->setEmail('letengu@outlook.fr');
		$user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
		$user->setCreatedAt(new \Datetime);
		$user->setFirstName($faker->firstName);
		$user->setLastName($faker->lastName);

		$manager->persist($user);
		

        $manager->flush();
    }
}
