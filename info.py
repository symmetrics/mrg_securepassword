# encoding: utf-8


# =============================================================================
# package info
# =============================================================================
NAME = 'symmetrics_module_securepassword'

TAGS = ('php', 'magento', 'module', 'symmetrics', 'mrg', 'password',
'passwort')

LICENSE = 'AFL 3.0'

HOMEPAGE = 'http://www.symmetrics.de'

INSTALL_PATH = ''


# =============================================================================
# responsibilities
# =============================================================================
TEAM_LEADER = {
    'Torsten Walluhn': 'tw@symmetrics.de',
}

MAINTAINER = {
    'Eric Reiche': 'er@symmetrics.de',
}

AUTHORS = {
    'Eric Reiche': 'er@symmetrics.de',
    'Yauhen Yakimovich': 'yy@symmetrics.de',
    'Siegfried Schmitz': 'ss@symmetrics.de',
}

# =============================================================================
# additional infos
# =============================================================================
INFO = 'Bindet verschiedene Passwortrichtlinien ein'

SUMMARY = '''
Unterbindet die Eingabe der Email Adresse als Passwort, schützt vor Bruteforce
Angriffen und bietet Hinweise, wie ein sicheres Passwort aussehen sollte.
'''

NOTES = '''
'''

# =============================================================================
# relations
# =============================================================================
REQUIRES = [
     {'magento': '*', 'magento_enterprise': '*'},
]

EXCLUDES = {
}

VIRTUAL = {
}

DEPENDS_ON_FILES = (
)

PEAR_KEY = ''

COMPATIBLE_WITH = {
     'magento': ['1.3.2.4', '1.4.0.0'],
     'magento_enterprise': ['1.3.2.4', '1.7.0.0', '1.7.1.0', '1.8.0.0'],
}