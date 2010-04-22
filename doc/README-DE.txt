* DOCUMENTATION

** INSTALLATION
Extrahieren Sie den Inhalt dieses Archivs in Ihr Magento Verzeichnis.

** USAGE
Dieses Modul verhindert, dass man die angegebene E-Mail Adresse auch
als Passwort verwenden kann.
Außerdem bietet es einen Brute-Force Schutz für das Login im
Frontend. Es sperrt nach mehreren fehlgeschlagenen Login-Versuchen
zeitweise den Account.
Im Registrierungsformular wird eine Notiz eingeblendet, die darauf
hinweist, wie ein sicheres Passwort aussehen sollte.

** FUNCTIONALITY
*** A: Gibt eine Fehlermeldung aus, wenn man versucht, bei der
        Registrierung das E-Mail und Password Feld identisch auszufüllen.
*** B: Sperrt das Benutzerkonto nach x fehlerhaften Einlog-Versuchen.
*** C: Fügt im Registrierungsformular eine Notiz hinzu, die beschreibt,
        wie ein sicheres Passwort sein sollte.

** TECHNICAL
*** A: Fängt das Event customer_save_before ab, prüft die beiden
        Felder und wirft bei Bedarf eine Exception.
*** B: Legt die Kunden-Attribute FailedLogins und LastFailedLogin an.
        Dort wird die Anzahl der Versuche und der Timestamp des letzten
        Versuchs gespeichert.
        Fängt das LoginPost PreDispatch und PostDispatch Event ab.
        Beim PreDispatch wird geguckt, ob der Benutzer gesperrt ist und
        ein eventueller  Login-Versuch abgebrochen.
        PostDispatch loggt fehlerhafte Versuche und setzt bei bedarf
        die beiden angelegten Attribute, sodass die PreDispatch Methode
        den Login-Versuch abbricht.
*** C: Über eine Layout-XML wird im Registrierungsformular im head die
        mrg/securepassword.phtml eingebunden. Diese Datei beinhaltet
        Javascript Code, der das Password Feld sucht und dort den Hinweis
        einfügt.

** PROBLEMS

* TESTCASES

** BASIC
*** A:
**** 1. Versuchen Sie als Passwort die Emailadresse bei der Registrierung
        zu verwenden.
**** 2. Sie sollten eine Fehlermeldung bekommen
**** 3. Versuchen Sie sich mit einem anderen Passwort zu registrieren.
*** B:
**** 1. Versuchen Sie sich 5mal mit dem falschen Passwort anzumelden.
**** 2. Beim nächsten Versuch sollte eine Meldung erscheinen, dass der
        Account gesperrt wurde, auch wenn Sie das korrekte Passwort eingeben.
        Nach 15 Minuten sollte die Sperre wieder aufgehoben sein. Wenn Sie sich
        in diesen 15 Minuten wieder versuchen, einzuloggen, fängt die
        Sperrzeit von vorn an.
*** C: Überpüfen Sie, ob beim Passwort-Feld der entsprechende Hinweis
        eingeblendet wird.
        
** CATCHABLE

** STRESS