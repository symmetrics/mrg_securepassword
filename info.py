# encoding: utf-8


# =============================================================================
# package info
# =============================================================================
NAME = 'symmetrics_module_securepassword'

TAGS = ('php', 'magento', 'module', 'symmetrics', 'mrg', 'password', 'passwort')

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
    'Yauhen Yakimovich': 'yy@symmetrics.de'
}

# =============================================================================
# additional infos
# =============================================================================
INFO = 'Unterbindet die Eingabe der Email Adresse als Passwort'

SUMMARY = '''
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
     'magento_enterprise': ['1.3.2.4', '1.7.0.0'],
}