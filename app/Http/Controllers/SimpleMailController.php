<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Mail;
    class SimpleMailController extends Controller
    {
        public function index() {
            $passingDataToView = 'Simple Mail Send In Laravel!';
            $data["email"] = 'adrianobr00@gmail.com';
            $data["title"] = "Mail Testing";

            Mail::send('emails.simplemail', ['passingDataToView'=> $passingDataToView], function ($message) use ($data){
                $message->to($data["email"],'John Doe');
                $message->subject($data["title"]);
            });;

            return 'Mail Sent';
        }
    }