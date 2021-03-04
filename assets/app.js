/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './sass/app.scss'
import './sass/lib/loading.scss'
import lazyLoadImages from "./lib/lazyLoading";
import axios from "axios";

// start the Stimulus application
import './bootstrap';
import './lib/contact-us/app'
import './lib/needCheck'
import './lib/admin/needToLogout'
import './lib/admin/users/user-actions'
import './lib/admin/profile/validateForm'
import './lib/admin/tableSearch'
import './lib/LoginAjax'
import './lib/loadMorePosts'
import './lib/loadImages'
import './lib/likePostAjax'
import './lib/lazyLoading'
import './lib/dislikePostAjax'
import './lib/AjaxRegister'
import './lib/admin/users/UserSuspensionModals'
import './lib/Notifications/MarkAllAsRead'
import './lib/users/update-password'
import './lib/loading'

window.axios = axios

lazyLoadImages()