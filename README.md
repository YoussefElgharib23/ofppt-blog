# new-admin-panel

### 1. Clone the project to your local directory 
```
$ git clone https://github.com/YoussefElgharib23/new-admin-panel
```

### 2. Install the project dependencies
```
$ composer install 
```

### 3. Create your mysql database
```
$ symfony console doctrine:database:create
```


### 4. Generate a migration table
```
$ symfony console make:migration
```

### 5. Migrate them to your database using doctrine
```
$ symfony console doctrine:migrations:migrate
```

### 6. Run the fllowing command using yarn or npm to install all the javascript dependencies for webpack
```
$ yarn | npm install --force
```

### 7. Run the fllowing command using yarn or npm to install to build the development assets
```
$ yarn | npm run dev
```

### 8. Run the fllowing command to open your project in the localserver
```
$ symfony server:start 
```

That's all !
Enjoy conding Hello
