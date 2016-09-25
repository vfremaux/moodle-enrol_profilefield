<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Adds new instance of enrol_paypal to specified course
 * or edits current instance.
 *
 * @package    enrol_profilefield
 * @category   enrol
 * @copyright  2010 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Capabilities.
$string['profilefield:config'] = 'Peut configurer le plugin d\'inscription par champ de profil';
$string['profilefield:enrol'] = 'Peut s\'inscrire par son profil';
$string['profilefield:manage'] = 'Peut ajouter une inscription par profil';
$string['profilefield:unenrol'] = 'Peut désinscrire une inscription par champ de profil';
$string['profilefield:unenrolself'] = 'Peut se désenroler du cours';

$string['assignrole'] = 'Assigner un role';
$string['auto_desc'] = 'Ce groupe a été automatiquement créé par la méthode d\'inscription par champ de profil.';
$string['badprofile'] = 'Désolé, mais votre profil ne vous permet pas d\'accéder à ce cours. Si vous devez y entrer pour une raison légitime, contactez les administrateurs de la plate-forme qui modifieront votre profil de façon adéquate.';
$string['auto'] = 'Automatique';
$string['auto_help'] = 'Si activé, l\'inscription est réalisés à priori sur connexion de l\'utilisateur sans qu\'il ait besoin de visiter le cours.';
$string['course'] = 'Cours : $a';
$string['enrolenddate'] = 'Date de fin';
$string['enrolenddate_help'] = 'Si elle est activée, cette date marque la fin de la période pendant laquelle les enrollements sont autorisés.';
$string['enrolenddaterror'] = 'La date de fin de la fenêtre d\'enrollement ne peut être avant son début';
$string['enrolme'] = 'M\'inscrire au cours';
$string['enrolmentconfirmation'] = 'Bienvenue. votre profil vous autorise la participation à ce cours. Voulez-vous vous inscrire ? ';
$string['enrolname'] = 'Inscription basée sur le profil utilisateur';
$string['enrolperiod'] = 'Durée de la période d\'enrollement';
$string['enrolperiod_help'] = 'Durée de l\'inscription, à partir de la date effective d\'enrollement. Si elle est désactivée, l\'inscription est illimitée dans le temps.';
$string['enrolstartdate'] = 'Date de début';
$string['enrolstartdate_help'] = 'Si elle est activée, les particicpants ne peivent s\'inscrire qu\'à partir de cette date.';
$string['emptyfield'] = 'Sans {$a}';
$string['grouppassword'] = 'Mot de passe pour l\'inscription à un groupe, s\'il est connu.';
$string['groupon'] = 'Grouper par';
$string['g_none'] = 'Pas de groupage, ou choisir...';
$string['g_auth'] = 'Méthode d\'autentification';
$string['g_dept'] = 'Départment';
$string['g_inst'] = 'Institution';
$string['g_lang'] = 'Langue';
$string['groupon_help'] = 'Cette méthode d\'authentification peut créer et mettre l\'utilisateur dans des groupes au moment de son inscription.';
$string['newcourseenrol'] = 'Un nouveau participant s\'est inscrit au cours {$a}';
$string['nonexistantprofilefielderror'] = 'Ce champ personnalisé de profil utilisateur n\'existe pas (ou plus)';
$string['notificationtext'] = 'Modèle de notification aux enseignants';
$string['notificationtext_help'] = 'Le contenu de la notification envoyée aux enseignants du cours peut être écrite ici, en utilisant des emplacements &lt;%%USERNAME%%&gt;, &lt;%%COURSE%%&gt;, &lt;%%URL%%&gt; and &lt;%%TEACHER%%&gt;. Notez que les balises multilingues seront traitées, selon la langue du destinataire.';
$string['notifymanagers'] = 'Notifier les enseignants ?';
$string['passwordinvalid'] = 'Le mot de passe est invalide';
$string['pluginname'] = 'Inscription basée sur le profil utilisateur';
$string['pluginname_desc'] = 'Cette méthode permet une inscription directe si un champ du profil utilisateur contient une valeur attendue.';
$string['profilefield'] = 'Champ du profil utilisateur';
$string['profilevalue'] = 'Valeur attendue';
$string['status'] = 'Activer l\'instance';
$string['unenrolself'] = 'Se désinscrire du cours "{$a}"?';
$string['unenrolselfconfirm'] = 'Voulez-vous vraiment vous désinscrire du cours "{$a}"?';

$string['defaultnotification'] = '
Bonjour <%%TEACHER%%>,

l\'utilisateur <%%USERNAME%%> s\'est inscrit (par correspondance de profil) dans votre cours <%%COURSE%%>.

Vous pouvez accéder à son profil <a href="<%%URL%%>">ici</a> après vous être connecté.
';