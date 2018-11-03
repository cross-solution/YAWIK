Feature: Update application settings
    In order to use Yawik
    As an administrator
    I should able to update yawik settings

    Background:
        Given I am logged in as an administrator

    @javascript @settings
    Scenario: Successfully change settings
        Given I go to settings page
        When I select "German" from "choose your language"
        And I select "Europe/Berlin" from "choose your timzone"
        And I press "Save"
        And I wait for the ajax response
        Then I should see "Changes successfully saved"

    @javascript @settings-email
    Scenario: Successfully change E-Mail Notifications Settings
        Given I go to email template settings page
        When I check "receive E-Mail alert"
        And I check "confirm application immidiatly after submit"
        And I check "get blind carbon copy of all own mails"
        And I wait for the ajax response
        And I fill in the following:
            | Mailtext               | Some Mailtext        |
            | Confirmation mail text | Confirmation Mail    |
            | Invitation mail text   | Invitation mail      |
            | Accept mail text       | Accept mail text     |
            | Rejection mail text    | Rejection mail text  |
        And I scroll "#yk-footer" into view
        And I press "Save"
        And I wait for the ajax response
        Then I should see "Changes successfully saved"
