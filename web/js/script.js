/**
 * This closure generates a password with 12 characters length. The password
 * contains one special character and one uppercase letter. This password will
 * be save for nearly all user accounts.
 */
document.getElementById("generate_password").onclick = function(){

    /**
     * A trick to create a password by base-36
     * Found at https://stackoverflow.com/questions/9719570/generate-random-password-string-with-requirements-in-javascript/9719815#9719815
     */
    password = Math.random()       // Generate random number, eg: 0.123456
                    .toString(36)   // Convert  to base-36 : "0.4fzyo82mvyr"
                    .slice(-11);     // Cut off last 10 characters : "yo82mvyr"

    // Get random position in string.
    pos_in_string = Math.floor((Math.random() * 11) + 1);

    // Get random index of the special characters array.
    arr_index_special_character = Math.floor((Math.random() * 4) + 0);

    // An array of special characters which shall be used for the password.
    arr_special_characters = ['*', '#', '%', '$'];

    // An array of numbers for comparison when setting one letter to uppercase.
    arr_numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    // Insert one random special character into the temporary password.
    password_with_special_character = password.insert(pos_in_string, arr_special_characters[arr_index_special_character]);

    // Split the password for the loop.
    arr_my_password = password_with_special_character.split('');

    // Empty variable for setting the password in it by the loop, character after character.
    ret_password = '';

    // Boolean to determine if one letter was already set to uppercase.
    bool_one_letter_set_to_uppercase = false;

    for (i = 0; i < arr_my_password.length; i++) {

        // If case to ensure that one letter was set to uppercase.
        if(arr_numbers.contains(arr_my_password[i]) === false &&
            arr_special_characters.contains(arr_my_password[i]) === false &&
            bool_one_letter_set_to_uppercase === false){
            arr_my_password[i] = arr_my_password[i].toUpperCase();
            bool_one_letter_set_to_uppercase = true;
        }

        ret_password = ret_password + arr_my_password[i];

    }

    // Set the password in the password form field.
    document.getElementById("password-password").value = ret_password;
};

// services:

/**
 * Insert a string to a position in string, which is specified by index.
 *
 * @param index
 * @param string
 * @returns {string}
 */
String.prototype.insert = function (index, string) {
    if (index > 0)
        return this.substring(0, index) + string + this.substring(index, this.length);
    else
        return string + this;
};

/**
 * Checks if an object/character is contained in array.
 *
 * @param obj
 * @returns {boolean}
 */
Array.prototype.contains = function(obj) {
    var i = this.length;
    while (i--) {
        if (this[i] === obj) {
            return true;
        }
    }
    return false;
}
