/**
 * This file is published from the "faithfm/laravel-simple-permissions" composer package.
 *    WARNING: Local modifications will be overwritten if the package is published during updates.
 *             See https://github.com/faithfm/laravel-simple-permissions for more details.
 */

// DEPENDENCY NOTE: We are expecting LaravelAppGlobals.users.permissions from our Laravel app, passed in via our Blade template.

// Return true if user has the specified permission - ie: "use-app"
export function laravelUserCan(permissionToCheck) {
    if (!checkUserPermissionsExist())   return false;
    return LaravelAppGlobals.user.permissions.some(p => p.permission===permissionToCheck);
}

// Return any restrictions associated with the specified permission
export function laravelUserRestrictions(permissionToCheck) {
    if (!checkUserPermissionsExist())   return false;
    const perm = LaravelAppGlobals.user.permissions.find(p => p.permission===permissionToCheck);
    if (perm === undefined)   return { status:"NOT PERMITTED" }
    if (!perm.restrictions )  return { status:"ALL PERMITTED" }
    try { return  { status:"SOME PERMITTED", ...perm.restrictions } }
    catch { return { status:"NOT PERMITTED", error:"ERROR DECODING RESTRICTIONS" } }   // note: now that JSON encoding is being handled by array-casting in the Model back-end, this error checking is less important.  (JSON errors now get converted by backend to NULLs without complaint)
}

// Check existence of user permissions 
export function checkUserPermissionsExist() {
    if (!window.LaravelAppGlobals)
        return console.log("'LaravelAppGlobals' variable missing. Ensure it is being correctly passed to the front-end."), false;    
    if (!LaravelAppGlobals.user)
        return console.log("User property missing from 'LaravelAppGlobals'. Ensure it is being correctly passed to the front-end."), false;
    if (!LaravelAppGlobals.user.permissions)
        return console.log("User permissions missing from 'LaravelAppGlobals.user'. Ensure the 'permissions' relationship in the User model is loaded (by default or explicitly) before passing the user to the front-end."), false;
    return true;
}
