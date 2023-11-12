## About Macroify

Macroify is a sub-domain based multi-tenant Employee Management, Training & Onboarding System.

### Super Admin
- Create tenants.
- Auto create databases when creating new tenants.
- Assign sub-domains for tenants.
- Create super-users.
  
### Tenant
- Create SOP (Standared Operation Procedures).
- Create Wikis.
- Create FAQs.
- Create Policy Handbooks and have employees acknowledge them.
- Create Users.
- Create Job Functions.
- Assign multiple Job Functions to a user.
- Group content by creating Categories.
- Manage users & job functions with departments.
- Manage content access based on Job Functions or Departments or make them available for everyone.
- ETC...

## Installation

Install dependancies
```
composer install
```

Run migrations
```
php artisan migrate --seed
```

Run tenant migrations
```
php artisan tenant:migrate --seed
```

Run server
```
php artisan serve
```
