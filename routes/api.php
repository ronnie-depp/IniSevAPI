<?php

use App\Models\User;
use App\Mail\FirstEmail;
use App\Mail\NewPost;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

// Route Group: api/v1
Route::prefix('/v1')->group(function () {

    // API Home for api/v1
    Route::get('/', function (Request $request) {
        $info = [
            'Application' => 'IniSev\'s API for Laravel 8.x Testing.',
            'Application Name env(\'APP_NAME\')' => env('APP_NAME'),
            'PHP Version'=>'7.3.12',
            'Laravel Version'=>'8.83.16'
        ];

        return response()->json($info)
            ->header('Content-Type', 'application/json');
    });

    // Get User by email
    Route::get('/user/{email}', function (User $user, $email) {
        $user = User::where('email', $email)->first();

        if(!$user) {
            return response()->json(['error' => $email.' not found.'])
                ->header('Content-Type', 'application/json');
        }

        return response()->json(['user' => $user->email])
            ->header('Content-Type', 'application/json');
    });

    // fetch all active websites
    Route::get('/fetch/websites', function (Request $request) {
        // Fetch All Active Websites. If `deleted_at` IS NOT NULL, that website is suspended/inactive, and will not be fetched.
        $websites = DB::select('select `site_name`, `site_url` from `websites` where `deleted_at` IS NULL');

        if(!$websites) {
            return response()->json(['error' => 'No websites found.'])
                ->header('Content-Type', 'application/json');
        }

        return response()->json($websites);//, 200);
    });

    // subscribe to a website
        /*
         *    Subscribing a User/email for a website
         *    by first using /api/v1/fetch/websites route
         *    to get a list of all available Active websites
         *    and then copy/paste a site_url of the desired website
         *    to subscribe to, using a valid email,
         *    without user verification email
         *    with 12 character verification code
         *    generated using Str::random(12)
         *    sent via email (for simplicity).
         *
         *    This route take to parameters in Request instance: (passed as url parameters)
         *
         *    @website
         *        The website user wants to subscribe to.
         *
         *    @email
         *        The valid email of the user.
         */
    Route::get('/subscribe', function (User $user, Request $request) {

        $website = $request->website;
        $email = $request->email;

        // First of all validate the website url and email
        // or throw json error
        $validated = $request->validate([
            'website' => 'required|url|active_url|max:255',// |active_url|unique:websites|
            'email' => 'required|email|max:255',// |email:rfc,dns|unique:users,email|
        ]);
        if(!$validated){
            return response()->json(['error' => 'Website ('.$website.') and/or Email ('.$email.') failed to validate.'])
                ->header('Content-Type', 'application/json');
        }

        // Clean up the website url for DB insertion
        if(strpos($website, "http://") !== false) $website = substr($website, 7, strlen($website));// strip `http://`.
        if(strpos($website, "https://") !== false) $website = substr($website, 8, strlen($website));// strip `https://`.
        if(strpos($website, "/") !== false) $website = substr($website, 0, strpos($website, "/"));// strip from 1st forward slash to end after `domain.tld/`.
        if(strpos($website, "?") !== false) $website = substr($website, 0, strpos($website, "?"));// remove all query strings
        if(strpos($website, "&") !== false) $website = substr($website, 0, strpos($website, "&"));//
        if(strpos($website, "=") !== false) $website = substr($website, 0, strpos($website, "="));//
        //$website = "'".$website."'";//add single qoutes
        // Now check if the given website exists
        // otherwise return json error

        $site = DB::select("select * from `websites` where `site_url` LIKE '".$website."' limit 1");
        //dump($website);
        //dump($site);

        if(!$site){
            // Create a website
            $siteInsert = DB::table('websites')->insert([//insertGetId([
                [
                    'site_name' => 'The Site: '.$website,
                    'site_url' => ''.$website,
                    'created_at' => now()
                ]
            ]);
            $site_id = DB::getPdo()->lastInsertId();
            /*
            return response()->json(['error' => 'Website ('.$website.') not found.'])
            ->header('Content-Type', 'application/json');
            */
            //dump($siteInsert);
            //dd($site_id);
        }

        // If User exists already, just subscribe to the website
        // by making an entry to the mapping table: `users_websites_roles`.
        // Otherwise just create new User for given email.

        /*$user = User::where('email', $email)->first();

        if(!$user) {
            return response()->json(['error' => $email.' not found.'])
            ->header('Content-Type', 'application/json');
        }*/
        $user = User::firstOrCreate(
            ['email'=> $email],
            ['subscription_status' => 'Active', 'subscribed_on' => now(),'created_at' => now()]
        );

        if($site){
            $site_id = $site[0]->id;
        }
        // Check if current User/Website/Role already exists
        if(DB::select("SELECT * FROM `users_websites_roles` WHERE `user_id` = ".$user->id." AND `website_id` = ".$site_id." AND `role_id` = 3 limit 1")){
            // tell User they are already Subscribed
            return response()->json(['msg' => 'User ('.$user->email.') is already subscribed to ('.$website.'). Try subscribing to another website.'])
                ->header('Content-Type', 'application/json');
        }

        // proceed with new entry in the mapping table
        // now subscribe user to the given website
        if(!$site){
            // Author as well as Subscriber
            DB::table('users_websites_roles')->insert([
                [
                    'user_id' => $user->id,
                    'website_id' => $site_id,
                    'role_id' => 2, // User Role: Author
                    'created_at' => now()
                ],
                [
                    'user_id' => $user->id,
                    'website_id' => $site_id,
                    'role_id' => 3, // User Role: Subscriber
                    'created_at' => now()
                ],// also assign default Authors and Subscribers to new Website so that other can also get updates.
                [
                    'user_id' => 2,
                    'website_id' => $site_id,
                    'role_id' => 2, // User Role: Author
                    'created_at' => now()
                ],
                [
                    'user_id' => 2,
                    'website_id' => $site_id,
                    'role_id' => 3, // User Role: Subscriber
                    'created_at' => now()
                ],
                [
                    'user_id' => 3,
                    'website_id' => $site_id,
                    'role_id' => 2, // User Role: Author
                    'created_at' => now()
                ],
                [
                    'user_id' => 3,
                    'website_id' => $site_id,
                    'role_id' => 3, // User Role: Subscriber
                    'created_at' => now()
                ],
                [
                    'user_id' => 4,
                    'website_id' => $site_id,
                    'role_id' => 3, // User Role: Subscriber
                    'created_at' => now()
                ],
                [
                    'user_id' => 5,
                    'website_id' => $site_id,
                    'role_id' => 2, // User Role: Author
                    'created_at' => now()
                ],
                [
                    'user_id' => 5,
                    'website_id' => $site_id,
                    'role_id' => 3, // User Role: Subscriber
                    'created_at' => now()
                ]
            ]);
        }
        else { // just a Subscriber
            DB::table('users_websites_roles')->insert([
                [
                    'user_id' => $user->id,
                    'website_id' => $site_id,
                    'role_id' => 3, // User Role: Subscriber
                    'created_at' => now()
                ]
            ]);
        }

        return response()->json(['msg' => 'User ('.$user->email.') subscribed to ('.$website.') successfully.'])
            ->header('Content-Type', 'application/json');

    });

    // Add Post to a Website.
    // Sending emails to All Subscribers using https://MailTrap.io
    //
    // Use this Artisan Command to dispatch queued jobs in the database:
    //      php artisan queue:work --queue=default,emails database
    //
    Route::post('post/new', function (User $user, Request $request) {

        $website = $request->website;
        $email = $request->email;
        $title = $request->title;
        $summary = $request->summary;
        $detail = $request->detail;

        // First of all validate the website url and email
        // or throw json error
        $validated = $request->validate([
            'website' => 'required|url|max:255',// |active_url|unique:websites|
            'email' => 'required|email|max:255',// |email:rfc,dns|unique:users,email|
            'title' => 'required|max:255',
            'summary' => 'required|max:500',
            'detail' => 'required',
        ]);
        if(!$validated){
            return response()->json(['error' => 'Website ('.$website.') and/or Email ('.$email.'), title, summary, detail has failed to validate.'])
                ->header('Content-Type', 'application/json');
        }

        // Request Params
        $title = urlencode(trim($title));
        $summary = urlencode(trim($summary));
        $detail = urlencode(trim($detail));
/*      $params = $request->except('_token');
        foreach($params as $param) {
            $param  = urlencode(trim($param));
        }
*/

        // Clean up the website url for DB insertion
        if(strpos($website, "http://") !== false) $website = substr($website, 7, strlen($website));// strip `http://`.
        if(strpos($website, "https://") !== false) $website = substr($website, 8, strlen($website));// strip `https://`.
        if(strpos($website, "/") !== false) $website = substr($website, 0, strpos($website, "/"));// strip from 1st forward slash to end after `domain.tld/`.
        if(strpos($website, "?") !== false) $website = substr($website, 0, strpos($website, "?"));// remove all query strings
        if(strpos($website, "&") !== false) $website = substr($website, 0, strpos($website, "&"));//
        if(strpos($website, "=") !== false) $website = substr($website, 0, strpos($website, "="));//

        // User
        $user = User::where('email', $email)->first();

        if(!$user) {
            return response()->json(['error' => 'user ('.$email.') not found.'])
            ->header('Content-Type', 'application/json');
        }

        // Website
        $site = DB::select("select * from `websites` where `site_url` LIKE '".$website."' limit 1");
        if($site){// Now check if the given website exists
            $site_id = $site[0]->id;
        }
        else {// otherwise return json error
            return response()->json(['error' => 'website ('.$website.') not found.'])
            ->header('Content-Type', 'application/json');
        }

        // Check if current User/Website/Role already an Author
        if(DB::select("SELECT * FROM `users_websites_roles` WHERE `user_id` = ".$user->id." AND `website_id` = ".$site_id." AND `role_id` = 2 limit 1")){
            // tell User they are already an Author.
            /*return response()->json(['msg' => 'User: ('.$user->email.') has Author Role already for Website: ('.$website.'). Required Role already is assigned.'])
                ->header('Content-Type', 'application/json');
            */
        } else {
            DB::table('users_websites_roles')->insert([
                [
                    'user_id' => $user->id,
                    'website_id' => $site_id,
                    'role_id' => 2, // User Role: Author
                    'created_at' => now()
                ],
                [
                    'user_id' => $user->id,
                    'website_id' => $site_id,
                    'role_id' => 3, // User Role: Subscriber
                    'created_at' => now()
                ]
            ]);
        }

        // Create Post to a Website
        DB::table('posts')->insert([
            [
                'post_title' => $title,
                'post_summary' => $summary,
                'post_detail' => $detail,
                'user_id' => $user->id,
                'website_id' => $site_id,
                'emails_sent' => 0,
                'created_at' => now()
            ]
        ]);
        $insertId = DB::getPdo()->lastInsertId();

        if(!empty($insertId) && is_numeric($insertId) && ($insertId > 0)){

            // To get subscribers list, join `users_websites_roles`, `websites`, `posts` and `users` tables.
            $joined = DB::table('users')
                ->join('users_websites_roles', 'users.id', '=', 'users_websites_roles.user_id')
                ->join('posts', 'posts.website_id', '=', 'users_websites_roles.website_id')
                ->join('websites', 'users_websites_roles.website_id', '=', 'websites.id')
                ->select('users.email', 'posts.*', 'users_websites_roles.role_id', 'websites.site_url')
                ->where([['users_websites_roles.role_id', 3], ['posts.website_id', $site_id], ['posts.emails_sent', 0]])
                /*->where('posts.website_id', $site_id)// this site's subscribers
                ->where('posts.emails_sent', 0)*/
                ->get();
            if($joined && !empty($joined)) {
                $recipients = $joined;
                //dump($joined);
            }

            // send email notification to Subscribers.
            foreach ($recipients as $recipient) {//['taylor@example.com', 'dries@example.com']
                $data = [
                    'title' => urldecode($recipient->post_title),
                    'summary' => urldecode($recipient->post_summary),
                    'detail' => urldecode($recipient->post_detail),
                    'email' => $recipient->email,
                    'user_id' => $recipient->user_id,
                    'site_id' => $recipient->website_id,
                    'site_url' => $recipient->site_url
                ];

                $message = (new NewPost($data))
                ->subject(urldecode($recipient->post_title))
                ->from($user->email)
                ->onConnection('database')
                ->onQueue('emails');

                Mail::to($recipient->email)
                    ->bcc('salman.test.inisevapi@gmail.com')
                    ->queue($message);
            }

            // update posts table: set emails_sent = 1
            if(!is_array(Mail::failures()) || (count(Mail::failures()) == 0)) {
                $affected = DB::table('posts')
                                ->where('emails_sent', 0)
                                ->update(['emails_sent' => 1]);
            }

            // return success JSON msg
            return response()->json(['msg' => 'User ('.$user->email.') created a Post to ('.$website.') successfully. Subscribers will be notified via email shortly.', 'Number of Posts emails were sent for:' => $affected, 'post' => ['title' => $title, 'summary' => $summary, 'detail' => $detail]])
                ->header('Content-Type', 'application/json');
        }
    });

    // Change Password
    Route::post('/user/change-password', function (Request $request, User $user) {
        $email = $request->email;
        $pwd = $request->password;
        $new_pwd = ($request->new_password === $request->confirm_new_password) ? $request->new_password : false;

        $user = User::where(['email'=> $email, 'pwd' => md5($pwd)])->first();
        // return json error if email/password don't match
        if(!$user) {
            return response()->json(['error' => 'Login and Change Password failed as your email/password do not match.'])
                ->header('Content-Type', 'application/json');
        }

        // return json error if new & confirm passwords don't match.
        elseif(!$new_pwd) {
            return response()->json(['error' => 'Change Password failed as your new/confirm passwords do not match.'])
                ->header('Content-Type', 'application/json');
        }

        // Do The Dew: update user's password to new one he/she supplied as POST param/variable/x-urlencoded-form value.
        else {
            if($user && $new_pwd) {
                $user->pwd = md5($new_pwd);
                $user->save();
                // Password Changed successfully.
                return response()->json(['user' => $user->email, 'msg' => 'Password Changed successfully.'])
                    ->header('Content-Type', 'application/json');
            }
            else {
                // return json error: unknown/unhandled exception has occured.
                return response()->json(['error' => 'An unknown/unhandled exception has occured.'])
                    ->header('Content-Type', 'application/json');
            }
        }
    });

    //
    Route::get('email/verification/code', function (Request $request) {
        return response()->json(['email_verification_code' => ''.Str::random(12)])
            ->header('Content-Type', 'application/json');
    });

    // 2 birds with one stone: email sending test using https://MailTrap.io
    //
    // Use this Artisan Command to dispatch queued jobs in the database:
    //      php artisan queue:work --queue=default,emails database
    //
    Route::get('email/sendmail/test', function () {

        $to_email = 'join@inisev.com';
        // Laravel Mail is working with MailTrap.io
        // Send a Raw Text Test Email
        Mail::raw('Our Mailinglist API is now live.', function($message) {
            $to_email = 'join@inisev.com';
            $message -> from('valentin@sellcodes.com', 'IniSev.com Team');
            $message -> to($to_email, 'Salman');
            $message -> subject('IniSevAPI');
        });

        // now test FirstEmail, a Queueable Mailable which is also always Queued.
        $message = (new FirstEmail())
                ->from('valentin@sellcodes.com', 'IniSev.com Team')
                ->onConnection('database')
                ->onQueue('emails');

        Mail::to($to_email, 'Salman Ahmad')
            ->send(new FirstEmail());
        //new FirstEmail();


         if (!is_array(Mail::failures()) || (count(Mail::failures()) == 0)) {
            // email sent success
            return response()->json(['msg' => 'Email successfully sent to ('.$to_email.').'])
                ->header('Content-Type', 'application/json');
        } else {
            // email sending failed
            return response()->json(['error' => 'Email failed to be sent to ('.$to_email.').'])
                ->header('Content-Type', 'application/json');
        }

        /*
        // Configured SendMail but Gmail and Hotmail are closing SMTP TLS connection with CRLs Certificate Revoked Lists.
        // So decided to use MailTrap.io for testing email functionality.

        // SendMail Test
        $to_email = "salman.test.inisevapi@gmail.com";//$to_email = "salman.test.inisevapi@gmail.com";
        $subject = "IniSevAPI";
        $body = "Mailinglist API is now live.";
        $headers = "From: Salman Ahmad <salman.test.inisevapi@gmail.com>\r\nReply-To: Valentin Kiryakov <valentin@sellcodes.com>";

        if (mail ($to_email, $subject, $body)) {
            // email sent success
            return response()->json(['msg' => 'Email successfully sent to ('.$to_email.').'])
                ->header('Content-Type', 'application/json');
        } else {
            // email sending failed
            return response()->json(['error' => 'Email failed to be sent to ('.$to_email.').'])
                ->header('Content-Type', 'application/json');
        }
        */
    });
});
