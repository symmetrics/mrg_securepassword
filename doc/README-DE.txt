* DOCUMENTATION

** INSTALLATION
Extrahieren Sie den Inhalt dieses Archivs in Ihr Magento Verzeichnis.

** USAGE
Dieses Modul verhindert, dass man die angegebene E-Mail Adresse auch
als Passwort verwenden kann.
Außerdem bietet es einen Brute-Force Schutz für das Login im
Frontend. Es sperrt nach mehreren (einstellbar) fehlgeschlagenen
Login-Versuchen zeitweise (einstellbar) den Account.
Im Registrierungsformular wird eine Notiz eingeblendet, die darauf
hinweist, wie ein sicheres Passwort aussehen sollte.

** FUNCTIONALITY
*** A: Gibt eine Fehlermeldung aus, wenn man versucht, bei der
        Registrierung das E-Mail und Password-Feld identisch auszufüllen.
*** B: Fügt im Registrierungsformular eine Notiz hinzu, die beschreibt,
        wie ein sicheres Passwort sein sollte.
*** C: Sperrt das Benutzerkonto nach x fehlerhaften Einlog-Versuchen.
        Wenn der Benutzer sich nach dem z.B. 4. Versuch richtig einloggt,
        wird der Zähler (für die falschen Versuche) auf 0 zurückgesetzt.
*** D: Fügt 3 Felder in der System Konfiguration unter
        "Admin Panel -> System -> Konfiguration -> Kunden -> 
        Kundenkonfiguration -> Passwortoptionen" ein, mit denen die
        Anzahl der fehlgeschlagenen Versuche, die Sperrzeit und die Versuchszeit
        geändert werden können.
        Wenn die Sperrzeit oder die Versuchszeit auf 0 steht, ist der
        Brute-Force Schutz de factor außer Kraft gesetzt.
*** E: Im Backend wird ein Feld hinzugefügt, womit der Kunde manuell
        entsperrt werden kann.

** TECHNICAL
A: Fängt das Event customer_save_before ab, prüft die beiden
        Felder und wirft bei Bedarf eine Exception.
B: Über eine Layout-XML wird im Registrierungsformular im Head die
        mrg/securepassword.phtml eingebunden. Diese Datei beinhaltet einen
        Javascript Code, der das Password Feld sucht und dort den Hinweis
        einfügt.
C: Legt die Kunden-Attribute FailedLogins und LastFailedLogin an.
        Dort wird die Anzahl der Versuche und der Timestamp des letzten
        Versuchs gespeichert.
        Fängt das LoginPost PreDispatch und PostDispatch Event ab.
        Beim PreDispatch wird geprüft, ob der Benutzer gesperrt ist und
        ein eventueller  Login-Versuch abgebrochen.
        PostDispatch loggt fehlerhafte Versuche und setzt bei bedarf
        die beiden angelegten Attribute, sodass die PreDispatch Methode
        den Login-Versuch abbricht.
D: Die 3 Felder werden über die system.xml angelegt.
E: Es werden über ein Migrationsskript 2 Felder angelegt: 
        last_unlock_time und unlock_customer.
        Letzteres kann in der Kundenverwaltung im Backend auf "ja" gestellt
        werden. Ein Observer fängt das Event customer_save_before ab und
        setzt das Feld auf "Nein" zurück und setzt die anderen beiden Felder 
        (s. Punkt B) wieder auuf 0, damit der Kunde sich einloggen kann.

** PROBLEMS
Der Passworthinweis im Registrierungsformular muss ggf. an das Design der 
Website angepasst werden.

* TESTCASES
** BASIC
*** A:
    1. Versuchen Sie als Passwort die Emailadresse bei der Registrierung
        zu verwenden.
    2. Sie sollten eine Fehlermeldung bekommen.
    3. Versuchen Sie sich mit einem anderen Passwort zu registrieren.
*** B: Überpüfen Sie, ob beim Passwort-Feld der entsprechende Hinweis
        eingeblendet wird.
*** C:
    1. Versuchen Sie sich fünf mal mit dem falschen Passwort anzumelden.
    2. Beim nächsten Versuch sollte eine Meldung erscheinen, dass der
        Account gesperrt wurde, auch wenn Sie das korrekte Passwort eingeben.
        Nach 15 Minuten sollte die Sperre wieder aufgehoben sein. Wenn Sie versuchen sich
        in diesen 15 Minuten wieder einzuloggen, fängt die
        Versuchszeit von vorn an.
*** D: 
    1. Prüfen Sie, ob die Felder vorhanden und speicherbar sind.
    2. Ändern Sie die Werte und prüfen Sie, ob sich die Funktionalität
        entsprechend der neuen Werte anpasst.
*** E: 
    1. Geben Sie das Passwort solange falsch ein, bis der Account gesperrt
        wird.
    2. Gehen Sie in die Benutzerverwaltung im Backend, stellen Sie das Feld
        "Benutzer entsperren" auf "ja" und speichern Sie den Kunden.
    3. Versuchen Sie sich im Frontend einzuloggen, das sollte jetzt ohne 
        Fehlermeldung funktionieren.
    4. Sie können weitere Kombinationen aus Zeit und Login-Versuchen
        kombinieren und prüfen, ob sich das Modul wie erwartet verhält.
