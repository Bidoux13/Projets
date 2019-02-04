<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationType;
use App\Form\ForgottenPasswordType;
use App\Form\ResetPasswordType;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    	return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserRepository $repo, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($repo->findOneByEmail($user->getEmail()) != null) {
                $this->addFlash('error', "Cet Email est déjà utilisé, veuillez en changer !");
                return $this->redirectToRoute('app_register');
            }
            $user->setCreatedAt(new \DateTime);
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', "Félicitation, vous êtes inscrit. Veuillez vous identifier !");
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/forgottenPassword", name="app_forgotten_password")
     */
    public function forgottenPassword(Request $request,
        UserRepository $repo,
        ObjectManager $manager,
        UserPasswordEncoderInterface $encoder,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $tokenGenerator
    ): Response
    {
        $user = new User();
        $form = $this->createForm(ForgottenPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $repo->findOneByEmail($user->getEmail());
            if ($user === null) {
                $this->addFlash('error', "Email inconnu !");
                return $this->redirectToRoute('app_login');
            }
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $manager->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('app_login');
            }

            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message('Obtenir un nouveau mot de passe !'))
                ->setFrom('letengu@orange.fr')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/reset_password.html.twig',[
                        'url' => $url,
                        'name' => $user->getUsername(),
                        ]),
                    'text/html');

            $mailer->send($message);
            $this->addFlash('success', "Mail envoyé ! Veuillez vérifier votre boite mail.");
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/forgotten_password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/reset_password/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, UserRepository $repo, string $token, UserPasswordEncoderInterface $passwordEncoder, ObjectManager $manager)
    {
        $user = new User;
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $user->getPassword();
            $user = $repo->findOneByResetToken($token);

            if ($user === null) {
                $this->addFlash('error', 'Token Inconnu !');
                return $this->redirectToRoute('homepage');
            }

            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $newPassword));
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', "Le mot de passe est mit à jour !");
            return $this->redirectToRoute('homepage');
        }
        return $this->render('security/reset_password.html.twig', [
            'form'  => $form->createView(),
            'token' => $token,
        ]);

    }

}
