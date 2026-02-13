<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{


    public function __construct(private UserRepository $userRepo){}


    #[Route('/api/register', name: 'app_sign_register', methods: 'POST')]
    public function inscription(Request $request, EntityManagerInterface $em): Response
    {

        $data = json_decode($request->getContent(), true);

        if (!$data){
            return $this->json(["status"=>"error", "message"=>"donne vide"]);
        }

        $newUser = new User();

        $newUser->setEmail($data["email"]);
        $newUser->setPassword(md5($data["password"]));
        $newUser->setPseudoMincraft($data["name"]);

        $token = md5(uniqid());
        $newUser->setToken($token);

        $em->persist($newUser);
        $em->flush();

        return $this->json(["status"=>"ok",
            "message"=>"user created",
            "result"=> [
                "UuidMincraft"=>$newUser->getId(),
                "PseudoMincraft"=>$newUser->getDisplayname(),
                "token"=>$token,
                "email"=>$newUser->getEmail()
            ]
        ]);

    }

    #[Route('/api/login', name: 'app_auth_login', methods: 'POST')]
    public function login(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data){
            return $this->json(["status"=>"error", "message"=>"donne vide"]);
        }

        $user = $this->userRepo->findOneBy(["email"=>$data["email"]]);

        if(!$user){
            return $this->json(["status"=>"error", "message"=>"user not found"]);
        }

        if(md5($data['password']) == $user->getPassword()){

            return $this->json(["status"=>"ok", "message"=>"login ok", "result"=>[
                "UuidMincraft"=>$user->getId(),
                "PseudoMincraft"=>$user->getPseudoMincraft(),
                "token"=>$user->getToken(),
                "email"=>$user->getEmail()
            ]]);


        } else {

            return $this->json(["status"=>"error", "message"=>"login failed, wrong password"]);

        }

    }


    #[Route('/api/me/', name: 'app_auth_token', methods: 'GET')]
    public function token(Request $request, EntityManagerInterface $em): Response
    {

        $token = $request->headers->get('Authorization');
        /* Authorization: Bearer <token>  */
        /*echo $token = Bearer <token> */

        if(!$token){
            return $this->json(["status"=>"error", "message"=>"token not found"]);
        }

        $token = substr($token, 7);

        $user = $this->userRepo->findOneBy(["token"=>$token]);

        if(!$user){
            return $this->json(["status"=>"error", "message"=>"user not found"]);
        }

        return $this->json(["status"=>"ok", "message"=>"connected", "result"=>
            [
                "UuidMincraft"=>$user->getUuidMincraft(),
                "PseudoMincraft"=>$user->getPseudoMincraft(),
                "token"=>$user->getToken(),
                "email"=>$user->getEmail()
            ]]);

    }



    #[Route('/api/me/', name: 'app_auth_update', methods: ['PUT'])]
    public function MiseàJour(Request $request, EntityManagerInterface $em): Response
    {
        $token = $request->headers->get('Authorization');

        if (!$token) {
            return $this->json(["status" => "error", "message" => "token not found"]);
        }

        $token = substr($token, 7);

        $user = $this->userRepo->findOneBy(["token" => $token]);

        if (!$user) {
            return $this->json(["status" => "error", "message" => "user not found"]);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(["status" => "error", "message" => "empty data"]);
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['name'])) {
            $user->setPseudoMincraft($data['name']);
        }

        if (isset($data['password'])) {
            $user->setPassword(md5($data['password']));
        }

        $em->flush();

        return $this->json([
            "status" => "ok",
            "message" => "user updated",
            "result" => [
                "UuidMincraft" => $user->getUuidMincraft(),
                "PseudoMincraft" => $user->getPseudoMincraft(),
                "token" => $user->getToken(),
                "email" => $user->getEmail()
        ]
    ]);
}








}
 


   

