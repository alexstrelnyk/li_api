##Deployment guideline

1. Create DB for the project.
2. Install Composer dependencies `composer install`
3. Copy .env.example to .env `cp .env.example .env`
4. composer install
5. Generate unique application key `php artisan key:generate`
4. Setup the next variables according to your environment
    - APP_URL=
    - DB_CONNECTION=
    - DB_HOST=
    - DB_PORT=
    - DB_DATABASE=
    - DB_USERNAME=
    - DB_PASSWORD=
    - MAIL_DRIVER=
    - MAIL_HOST=
    - MAIL_PORT=
    - MAIL_USERNAME=
    - MAIL_PASSWORD=
    - MAIL_ENCRYPTION=
    - MAIL_FROM_ADDRESS=
    - MAIL_FROM_NAME=
5. Migrate the database `php artisan migrate`

### Links

After deployment the Swagger API documentation will be available on `https://{your-domain-name}/api/docs`

### Populating database by test data

`php artisan db:seed`

After this available next credentials with simply generated tokens

`admin@admin.com` - Li Admin - *LI_ADMIN* role - `admin` token
`editor@editor.com` - Li Editor - *LI_EDITOR* role - `editor` token
`user@user.com` - Application User - *APP_USER* role - `user` token

These tokens can be used in Swagger documentation also in format `Bearer {token}`

## **API**

## Authorization Process

There are 3 endpoints that perform next process: Getting magicLink, use magicLink for verification, setup Authorization Bearer token.

1. Invite Email [POST] /invite/send (Use link for getting verification token)
2. Sign In [POST] /auth/verify/{token} (Use verification token for getting access token)
3. Use access token in Authorization header for performing the requests

## Authorization for a non-existing user
1. Invite Emails [POST] /invite/send ("User not found message should be appears")
2. Authorize in the system as LI Admin or Client Admin (Need for getting the list of Invite Emails and Clients)
3. Invite Emails [GET] /invite (Get the list of invite emails)
4. Clients [GET] /clients (Get the list of clients)
5. Invite Emails [POST] /invite/assign (Use "client_id" and "invite_email_Id")
This operation will create the new User and assign them to Client

Now you can use this email for authorization