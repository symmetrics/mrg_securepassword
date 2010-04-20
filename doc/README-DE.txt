* DOCUMENTATION

** INSTALLATION
Extrahieren Sie den Inhalt dieses Archivs in Ihr Magento Verzeichnis.

** USAGE
Dieses Modul verhindert, dass man die angegebene E-Mail Adresse auch
als Passwort verwenden kann.

** FUNCTIONALITY
*** A: Gibt eine Fehlermeldung aus, wenn man versucht, bei der Registrierung
        das E-Mail und Password Feld identisch auszufüllen.

** TECHNICAL
Fängt das Event customer_save_before ab, prüft die beiden Felder und wirft
bei Bedarf eine Exception.

** PROBLEMS

* TESTCASES

** BASIC
*** A:
**** 1. Versuchen Sie als Passwort die Emailadresse bei der Registrierung zu verwenden.
**** 2. Sie sollten eine Fehlermeldung bekommen
**** 3. Versuchen Sie sich mit einem anderen Passwort zu registrieren.

** CATCHABLE

** STRESS