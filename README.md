# IniSevAPI for Mailing List (Laravel 8.83.16 / PHP 7.3.12)

#### You need to configure your `.env` file and other files in `config` directory to get started.
- first set your database email configuration to get the application up and running.
- I'm using `php artisan serve --port=1920`. You can change your port number as desired. But be sure to update all request paths in `Postman`.
- For documentation see `Postman Collection` Documentation with Examples included in `Postman Collection` file in `IniSevAPI/IniSevAPI.postman_collection.json` and code comments in `routes/api.php` file and migration files. Please import this file in `Postman`: https://github.com/ronnie-depp/IniSevAPI/blob/master/IniSevAPI.postman_collection.json
- Table `posts` can further be normalized by spliting it in to another table for `user_id`, `website_id` and `post_id`.
- I'm using https://mailtrap.io for email functionality. Because when using `SMTP` with `SendMail` configured, GMAil and Hotmail return CRLs (`Cerificate Revoke Lists`) after successfully establishing connection using TLS (`Transport Layer Security`) handshakes.
- I use the `Artisan` command: `php artisan queue:work --queue=default,emails database` to monitor and send/dispatch all queued email jobs. `emails` Queue is used for Mailing List emails when using `[Create Post](http://localhost:1920/api/v1/post/new)` `http://localhost:1920/api/v1/post/new` request. `default` queue is for 2 test emails sent when using `http://localhost:1920/api/v1/email/sendmail/test` request. `database` Queue driver is being used.
- 

# Original Requirements

Create a simple subscription platform(only RESTful APIs with MySQL) in which users can subscribe to a website (there can be multiple websites in the system). Whenever a new post is published on a particular website, all it's subscribers shall receive an email with the post title and description in it. (no authentication of any kind is required)

MUST:-
- Use PHP 7.* (i.e. use Laravel 8 as Laravel 9 requires PHP 8.0)
- Write migrations for the required tables.
- Endpoint to create a "post" for a "particular website".
- Endpoint to make a user subscribe to a "particular website" with all the tiny validations included in it.
- Use of command to send email to the subscribers.
- Use of queues to schedule sending in background.
- No duplicate stories should get sent to subscribers.
- Deploy the code on a public github repository.

OPTIONAL:-
- Seeded data of the websites.
- Open API documentation (or) Postman collection demonstrating available APIs & their usage.
- Use of contracts & services.
- Use of caching wherever applicable.
- Use of events/listeners.

Note:- 
1. Please provide special instructions(if any) to make to code base run on our local/remote platform.
2. Only implement what is mentioned in brief, i.e. only the API, no front-end things etc. The codes will never be deployed, we just want to see your coding skills. 
3. There isn't a strict deadline. The faster the better, however code quality (and implementing it as mentioned in brief) is the most important. However, of course it shouldn't take more than a couple of hours. 
4. If anything isn't clear, just implement it according to your understanding. There won't be any further explanations, the task is clear. As long as what you do doesn't contradict the briefing, it's fine. 
