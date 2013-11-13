<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** AttachmentsFieldset.php */ 
namespace Applications\Form;

use Zend\Form\Fieldset;

class AdministrationFieldset extends Fieldset
{
    
     
    public function init()
    {
        $this->setName('privacypolicies')
             ->setLabel('Privacy Policies');
                     
          $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'privacyPolicyAccepted',
        		'options' => array('label' => /* @translate */ 'Privacy Policy',
                            'description' => 
                                'Die mediaintown GmbH &amp; Co. KG stellt
                                ihren Kunden ein System zur Entgegennahme und Verwaltung von
                                Online(Internet)-Bewerbungen zur Verfügung und legt hier großen Wert
                                auf den Schutz Ihrer personenbezogenen Daten. Daher informieren wir
                                Sie, gemäß den einschlägigen Datenschutzvorschriften, insbesondere dem
                                Bundesdatenschutzgesetz (BDSG), über die Erhebung, Verarbeitung und
                                Nutzung Ihrer Daten im Rahmen der Online-Bewerbung.<br />
                                Bitte lesen Sie die folgenden Informationen und Bestimmungen
                                aufmerksam durch, bevor Sie Ihre Daten übermitteln. <br />
                                <h3>Datenerhebung</h3>
                                Möchten Sie sich online auf eine Anzeige unseres Kunden über das
                                Bewerbermanagement bewerben, so benötigt unser Kunde hierzu einige
                                Angaben zu Ihrer Person. Die Datenerhebung erfolgt im Sinne von §32
                                BDSG. Im Rahmen der Online-Bewerbung werden Bewerberdaten erhoben und
                                verarbeitet, welche für eine effektive und korrekte Abwicklung des
                                Bewerbungsverfahrens notwendig sind. Im Bewerbungsprozess werden Daten
                                teilweise zwingend erhoben (Pflichtfelder). Ihre Bewerberdaten werden
                                jederzeit vertraulich behandelt. Darüber hinaus weisen wir Sie darauf
                                hin, dass die von Ihnen übermittelten Daten ggf. zur Erstellung von
                                Statistiken über den (Online-)Bewerbungsprozess verwendet werden
                                können. Die Erstellung dieser Statistiken erfolgt ausschließlich zu
                                internen Zwecken und erfolgt in keinem Fall personalisiert sondern in
                                anonymisierter Form.
                                <h3>Zweck der Erhebung und Übermittlung von Daten</h3>
                                Die Bewerbungsdaten werden zum Zweck der Bewerbungsabwicklung durch
                                unseren Kunden erhoben und verarbeitet.
                                <h3>Datenlöschung</h3>
                                Die Löschung der übermittelten Daten aus diesem
                                Bewerbermanagementsystem erfolgt automatisch längstens 6 Monate nach
                                Beendigung des Bewerbungsverfahrens. Dieses gilt nicht, sofern
                                gesetzliche Bestimmungen der Löschung entgegenstehen oder die weitere
                                Speicherung zum Zwecke der Beweisführung erforderlich ist oder Sie
                                einer längeren Speicherung zugestimmt haben. Sollte die Löschung nur
                                mit unverhältnismäßig großem Aufwand möglich sein, so tritt anstelle
                                der Löschung die Sperrung der Daten.
                                <h3>Verschlüsselung</h3>
                                Aufgrund der Sensibilität der Daten erfolgt die Übertragung in
                                verschlüsselter Form.
                                <h3>Auskunft &amp; Widerruf</h3>
                                Sie haben jederzeit ein Widerrufsrecht zu den erteilten Einwilligungen
                                und das Recht, jederzeit Auskunft über die gespeicherten Informationen
                                zu erhalten. Zur Wahrnehmung der vorgenannten oder sonstiger den
                                Datenschutz betreffenden Rechte wenden Sie sich bitte schriftlich per
                                E-Mail an unseren Kunden.<br />
                                Die mediaintown GmbH &amp; Co. KG hat keinerlei Einsicht in und
                                Zugriff auf die Bewerbungsunterlagen und -daten, da diese
                                ausschließlich innerhalb des Bewerbungsverfahrens über einen
                                verschlüsselten Zugang unserem Kunden zur Verfügung stehen.<br />
                                Fragen zum Bewerbungsprozess können wir nicht beantworten.
                                <h3>Einwilligung</h3>
                                Mit Aktivieren der Check-Box erklären Sie sich ausdrücklich damit
                                einverstanden, dass unser Kunde die von Ihnen über das
                                Bewerbermanagementsystem übermittelten Daten zum Zwecke der
                                Bewerbungsabwicklung gemäß §32 BDSG erheben, verarbeiten und nutzen
                                darf. Eine Übermittlung Ihrer Daten erfolgt nur dann, wenn Sie Ihre
                                Einwilligung durch Aktivieren der Check-Box bestätigt haben.<br />
                                Hinweis zu sensiblen Daten: Wir weisen Sie ausdrücklich darauf hin,
                                dass Bewerbungen, insbesondere Lebensläufe, Zeugnisse und weitere von
                                Ihnen übermittelte Daten, besonders sensible Angaben über geistige und
                                körperliche Gesundheit, rassische oder ethnische Herkunft, zu
                                politischen Meinungen, religiösen oder philosophischen Überzeugungen,
                                Mitgliedschaften in einer Gewerkschaft oder politischen Partei oder
                                zum Sexualleben enthalten können.<br />
                                Übermitteln Sie solche Angaben in Ihrer Online-Bewerbung, so erklären
                                Sie sich ausdrücklich damit einverstanden, dass unser Kunde diese
                                Daten, zum Zwecke der Bewerbungsabwicklung, erheben, verarbeiten und
                                nutzen darf. Die Verarbeitung dieser Daten erfolgt in Übereinstimmung
                                mit dieser Datenschutzerklärung und den sonstigen einschlägigen
                                Rechtsvorschriften.<br />
                                Wir machen darauf aufmerksam, dass bei Datenübertragungen im Internet
                                Sicherheitsrisiken nicht ausgeschlossen werden können.
                                <h3>Ansprechpartner / Datenschutzbeauftragter</h3>
                                Haben Sie Fragen zum Datenschutz wenden Sie sich bitte an <a href="mailto:info@mediaintown.de">info@mediaintown.de</a>.',
                            )));
    }
}

