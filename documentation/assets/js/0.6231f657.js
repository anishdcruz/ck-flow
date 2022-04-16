(window.webpackJsonp=window.webpackJsonp||[]).push([[0],{50:function(e,t,a){"use strict";a.r(t);var r=a(0),n=Object(r.a)({},function(){this.$createElement;this._self._c;return this._m(0)},[function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"content"},[a("h1",{attrs:{id:"documentation"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#documentation","aria-hidden":"true"}},[e._v("#")]),e._v(" Documentation")]),a("div",{staticClass:"tip custom-block"},[a("p",{staticClass:"custom-block-title"},[e._v("TIP")]),a("p",[e._v("For support email us at: codekerala@gmail.com")])]),a("h2",{attrs:{id:"getting-started"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#getting-started","aria-hidden":"true"}},[e._v("#")]),e._v(" Getting Started")]),a("p",[e._v("Lumen is a simple invoicing solution built on top of "),a("a",{attrs:{href:"https://laravel.com/",target:"_blank",rel:"noopener noreferrer"}},[e._v("Laravel PHP")]),e._v(", "),a("a",{attrs:{href:"https://vuejs.org/",target:"_blank",rel:"noopener noreferrer"}},[e._v("Vue.js")]),e._v(" and Webpack. Lumen follows standard laravel practices for development and delopyment.")]),a("h2",{attrs:{id:"system-requirements"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#system-requirements","aria-hidden":"true"}},[e._v("#")]),e._v(" System Requirements")]),a("p",[e._v("You will need to make sure your server meets the following requirements:")]),a("ul",[a("li",[e._v("Linux Operating System")]),a("li",[e._v("Nginx / Apache")]),a("li",[e._v("PHP >= 7.1.3")]),a("li",[e._v("OpenSSL PHP Extension")]),a("li",[e._v("PDO PHP Extension")]),a("li",[e._v("Mbstring PHP Extension")]),a("li",[e._v("Tokenizer PHP Extension")]),a("li",[e._v("XML PHP Extension")]),a("li",[e._v("Ctype PHP Extension")]),a("li",[e._v("JSON PHP Extension")]),a("li",[e._v("MYSQL 5.7+")])]),a("p",[e._v("Lumen utilizes "),a("a",{attrs:{href:"https://getcomposer.org/",target:"_blank",rel:"noopener noreferrer"}},[e._v("Composer")]),e._v(" to manage its dependencies. So, before using Lumen, make sure you have Composer installed on your machine.")]),a("h2",{attrs:{id:"installation"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#installation","aria-hidden":"true"}},[e._v("#")]),e._v(" Installation")]),a("h3",{attrs:{id:"public-directory"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#public-directory","aria-hidden":"true"}},[e._v("#")]),e._v(" Public Directory")]),a("p",[e._v("After installing Lumen, you should configure your web server's document / web root to be the  public directory. The index.php in this directory serves as the front controller for all HTTP requests entering your application.")]),a("h3",{attrs:{id:"directory-permissions"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#directory-permissions","aria-hidden":"true"}},[e._v("#")]),e._v(" Directory Permissions")]),a("p",[e._v("Directories within the storage and the bootstrap/cache directories should be writable by your web server or Lumen will not run.")]),a("p",[e._v("We can give group ownership of our Lumen directory structure to the web group by typing:")]),a("pre",{pre:!0,attrs:{class:"language-text"}},[a("code",[e._v("sudo chown -R :www-data ./lumen\nsudo chmod -R 775 ./lumen/storage\nsudo chmod -R 775 /.lumen/bootstrap/cache\n")])]),a("h3",{attrs:{id:"application-config"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#application-config","aria-hidden":"true"}},[e._v("#")]),e._v(" Application Config")]),a("p",[e._v("Next create a new database and add your database credentials to your .env file:")]),a("pre",{pre:!0,attrs:{class:"language-text"}},[a("code",[e._v("DB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=deployer\nDB_USERNAME=deployer\nDB_PASSWORD=secret\n")])]),a("p",[e._v("You will also want to update your website URL inside of the APP_URL variable inside the .env file:")]),a("pre",{pre:!0,attrs:{class:"language-text"}},[a("code",[e._v("APP_URL=http://www.example.com\n")])]),a("h3",{attrs:{id:"email"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#email","aria-hidden":"true"}},[e._v("#")]),e._v(" Email")]),a("p",[e._v("Also setup your email server config in .env to send emails.")]),a("pre",{pre:!0,attrs:{class:"language-text"}},[a("code",[e._v("MAIL_DRIVER=smtp\nMAIL_HOST=smtp.mailtrap.io\nMAIL_PORT=2525\nMAIL_USERNAME=null\nMAIL_PASSWORD=null\nMAIL_ENCRYPTION=null\n")])]),a("p",[e._v("You may also want to configure a few additional components of Laravel, such as: Cache, Session, etc.")]),a("h1",{attrs:{id:"run-the-installer"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#run-the-installer","aria-hidden":"true"}},[e._v("#")]),e._v(" Run The Installer")]),a("p",[e._v("Lastly, we can install lumen by running the below.")]),a("pre",{pre:!0,attrs:{class:"language-text"}},[a("code",[e._v("php artisan lumen:install\n")])]),a("p",[e._v("The above comand will also run php artisan "),a("code",[e._v("key:generate --force")]),e._v(" and "),a("code",[e._v("php artisan migrate --force")])]),a("p",[e._v("By default you should be able to login with the below email and password")]),a("pre",{pre:!0,attrs:{class:"language-text"}},[a("code",[e._v("Email: admin@lumen.test\nPassword: password\n")])]),a("div",{staticClass:"warning custom-block"},[a("p",{staticClass:"custom-block-title"},[e._v("WARNING")]),a("p",[e._v("Please change your email and password inside user settings after login.")])]),a("h3",{attrs:{id:"cron-job"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#cron-job","aria-hidden":"true"}},[e._v("#")]),e._v(" Cron Job")]),a("p",[e._v("In order to make "),a("code",[e._v("recurring exports")]),e._v(" work, you have to setup up cron job.\nGo to your terminal and run the below command")]),a("pre",{pre:!0,attrs:{class:"language-text"}},[a("code",[e._v("crontab -e\n")])]),a("p",[e._v("This will open server crontab file, paste this code inside, save it and exit.")]),a("div",{staticClass:"warning custom-block"},[a("p",{staticClass:"custom-block-title"},[e._v("WARNING")]),a("p",[e._v("Make sure the path is correct")])]),a("pre",{pre:!0,attrs:{class:"language-text"}},[a("code",[e._v("* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1\n")])]),a("p",[e._v("This Cron will call the Lumen command scheduler every minute. When the "),a("code",[e._v("schedule:run")]),e._v(" command is executed, Lumen will evaluate your scheduled tasks and runs the tasks that are due.")]),a("h3",{attrs:{id:"pretty-urls"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#pretty-urls","aria-hidden":"true"}},[e._v("#")]),e._v(" Pretty URLs")]),a("h4",{attrs:{id:"apache"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#apache","aria-hidden":"true"}},[e._v("#")]),e._v(" Apache")]),a("p",[e._v("Lumen includes a "),a("code",[e._v("public/.htaccess")]),e._v(" file that is used to provide URLs without the "),a("code",[e._v("index.php")]),e._v(" front controller in the path. Before serving Lumen with Apache, be sure to enable the "),a("code",[e._v("mod_rewrite")]),e._v(" module so the "),a("code",[e._v(".htaccess")]),e._v(" file will be honored by the server.")]),a("p",[e._v("If the "),a("code",[e._v(".htaccess")]),e._v(" file that ships with Lumen does not work with your Apache installation, try this alternative:")]),a("pre",{pre:!0,attrs:{class:"language-text"}},[a("code",[e._v("Options +FollowSymLinks\nRewriteEngine On\n\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteRule ^ index.php [L]\n")])]),a("h4",{attrs:{id:"nginx"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#nginx","aria-hidden":"true"}},[e._v("#")]),e._v(" Nginx")]),a("p",[e._v("If you are using Nginx, the following directive in your site configuration will direct all requests to the "),a("code",[e._v("index.php")]),e._v(" front controller:")]),a("pre",{pre:!0,attrs:{class:"language-text"}},[a("code",[e._v("location / {\n    try_files $uri $uri/ /index.php?$query_string;\n}\n")])]),a("h2",{attrs:{id:"configuration"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#configuration","aria-hidden":"true"}},[e._v("#")]),e._v(" Configuration")]),a("h2",{attrs:{id:"for-developers"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#for-developers","aria-hidden":"true"}},[e._v("#")]),e._v(" For developers")]),a("p",[e._v("Lumen is a build with laravel 5.6 and Vue js")]),a("p",[e._v("This zip file only contain production files and does not contain node_modules director.\nIn order to customize you have delete the vendor directory and run below commands:")]),a("pre",{pre:!0,attrs:{class:"language-text"}},[a("code",[e._v("composer install\nnpm install\n")])]),a("h3",{attrs:{id:"directory-structure"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#directory-structure","aria-hidden":"true"}},[e._v("#")]),e._v(" Directory Structure")]),a("ul",[a("li",[e._v("Javascript files can be found in "),a("code",[e._v("resources/assets/js")]),e._v(" and not in the public folder.")])]),a("h2",{attrs:{id:"internationalization"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#internationalization","aria-hidden":"true"}},[e._v("#")]),e._v(" Internationalization")]),a("p",[e._v("You can find the english translation inside "),a("code",[e._v("resources/lang/en")])]),a("p",[e._v("For example to translate to language french "),a("code",[e._v("fr")])]),a("ul",[a("li",[e._v("set "),a("code",[e._v("APP_LOCALE=fr")]),e._v(" inside "),a("code",[e._v(".env")])]),a("li",[e._v("Make a folder "),a("code",[e._v("fr")]),e._v(" inside "),a("code",[e._v("resources/lang/")])]),a("li",[e._v("copy all the files from "),a("code",[e._v("resources/lang/en")]),e._v(" to "),a("code",[e._v("resources/lang/fr")])]),a("li",[e._v("Begin translation")])]),a("h2",{attrs:{id:"security"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#security","aria-hidden":"true"}},[e._v("#")]),e._v(" Security")]),a("p",[e._v("Before you deploy make sure you have set proper settings in "),a("code",[e._v(".env")]),e._v(" file")]),a("pre",{pre:!0,attrs:{class:"language-text"}},[a("code",[e._v("APP_ENV=production\nAPP_DEBUG=false\n")])])])}],!1,null,null,null);t.default=n.exports}}]);