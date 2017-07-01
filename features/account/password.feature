Feature: Change user password
    In order to enhance the security of my account
    As a User
    I want to be able to change my password

    Background:
        Given there is a user with the following:
            | email         | test@password.com       |
            | login         | test.password           |
            | password      | test                    |
            | fullname      | Test Password           |
        And I log in with username "test@password.com" and password "test"

    Scenario: Changing my password
        When I want to change my password
        And I fill in the following:
            | Password        | test |
            | Retype password | test |
        And I press "Save"
        And I wait for the ajax response
        Then I should see "Password successfully changed"