* DOCUMENTATION

** INSTALLATION
Extract the content of this archive to your Magento directory.

** USAGE
This module prevents from using the specified e-mail address also 
as password.
Besides, it offers a brute force protection for the login in
frontend. It temporarily (configurable) blocks an account after 
several (configurabe) failed attempts to log in.

** FUNCTIONALITY
*** A: Displays an error message when upon registration it is attempted 
       to fill in the e-mail and password fields identically.
*** B: Adds a note to the registration form that describes
       how a secure password should be.
*** C: Blocks the user account after x failed login attempts.
       When the user after for example 4th attempt logs in correctly,
       the counter (for the wrong attempts) is set on 0.
*** D: Adds 3 fields in the system configuration under
       Admin panel-> system → configuration → customers ->
       customer configuration → password options”
       with which the number of failed attempts, the block time and
	   the time for attempts can be changed.
        When the block time or the time for attempts is on 0, the
        brute force protection is de facto suspended.
*** E: In backend a field is added with which the customer can be
         manually unblocked.

** TECHNICAL
A: Catches the event  customer_save_before, checks the both
        fields and when necessary throws an exception.
B: Through a layout-XML the  mrg/securepassword.phtml is integrated
        in registration form in head. This data contains a
        Javascript code, that searches a password field and adds a note
        there.
C: Creates the customer attributes FailedLogins and LastFailedLogin.
        The number of attempts and the timestamp of the last attempt is
        saved there.
        Catches the LoginPost PreDispatch and PostDispatch event.
        Upon  PreDispatch it is checked if the user is locked and
        a possible login attempt aborted.
        PostDispatch logs failed attempts and when necessary sets
        the both created attributes, so that the PreDispatch method
        aborts the login.
D: The 3 fields are created through the  system.xml.
E: 2 fields are created through the migrations script: 
        last_unlock_time and unlock_customer.
        The latter can be set on “yes” in the customer management in
        backend. An observer catches the event customer_save_before,
        and sets the field back on “no” and sets other fields (see item B) 
        again on 0, so that the customer can login.

** PROBLEMS
The password note in registration form must when appropriate  
be adapted to the design of the web site.

* TESTCASES
** BASIC
*** A:
    1. Upon the registration, try to use the e-mail address as password.
    2. You should get an error message.
    3. Try to register with another password.
*** B: Check if a corresponding note appears at the password field. 
*** C:
    1. Try to login with the wrong password five times.
    2. Upon the next attempt a message should appear that the account 
        has been blocked,  even if you enter a correct password.
        After 15 minutes the block should be disabled again. If you try 
        to login after these 15 minutes again, the time for attempts 
        starts  from the beginning.
*** D: 
    1. Check if the fields are available and can be saved..
    2. Change the values and check that the functionality is updated
        according to the new values.
*** E: 
    1. Enter the wrong password as long as the account gets blocked.
    2.Go to customer management in backend, set the “Benutzer entsperren"
        (“Unlock customer”)  field on “yes” and save.
    3. Try to login in frontend, this should work without the error  
        message now.
    4. You can combine and check other combinations of time and login attempts,
        in order to check if the behavior of module is as expected.
