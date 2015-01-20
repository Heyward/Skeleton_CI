<?php
class AuthConstants
{
    const ERROR_LOGIN   = "auth_error";
    const USER_ID       = "auth_user_id";
    const EMAIL         = "auth_email";
    const NAMES         = "auth_names";
    const LAST_NAMES    = "auth_last_names";
    const ADMIN         = "auth_admin";
    const ADMIN_OK      = "1";
    const ADMIN_KO      = "0";
    const YES           = "YES";
    const NO            = "NO";
    const PROFILE       = "auth_profile";
    const LANG          = "lang";
    const PROFILE_ADMIN = "Admin";
    const WORKSHOPS     = "user_workshops";
    const COUNTRY       = "user_country";
    
    //IDS PROFILES
    const ID_PROFILE_ADMIN      = 1;
    const ID_PROFILE_ATTENDANT  = 2;
    const ID_PROFILE_USER       = 3;
    const ID_PROFILE_WORKSHOP_ADMIN = 4;
    
    //APPOINTMENTS STATUS
    const STATUS_SCHEDULED  = 'scheduled';
    const STATUS_CANCELED   = 'canceled';
    const STATUS_COMPLETED  = 'completed';
    
    //VEHICLES STATUS
    const VEHICLE_ACTIVE    = 'active';
    const VEHICLE_INACTIVE  = 'inactive';
    const VEHICLE_INPROCESS = 'in_process';
}
?>