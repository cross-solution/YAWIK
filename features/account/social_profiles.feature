Feature: Social profile feature
    In order to use social profile information
    As a User
    I should able to manage my social profiles

    Background:
        Given there is a user with the following:
            | email         | test@social-profile.com |
            | password      | test                    |
            | fullname      | Test Social Profile     |
        And I log in with username "test@social-profile.com" and password "test"

    @javascript @profile-facebook @social-profile
    Scenario: Link to facebook
        When I go to profile page
        And I press "Facebook"
        And I wait for the ajax response
        And I switch to popup "fetch social profile"
        And I fill in login form with Facebook user
        And I press "loginbutton"
        And I wait for 5 seconds
        And I switch back to main window
        And I wait for the ajax response
        Then I should see an ".btn-success .fa-facebook" element

    @javascript @profile-linkedin @social-profile
    Scenario: Link to LinkedIn
        When I go to profile page
        And I press "LinkedIn"
        And I wait for the ajax response
        When I switch to popup "fetch social profile"
        And I wait for the ajax response
        And I fill in login form with LinkedIn user
        And I press "Sign In"
        And I wait for the ajax response
        And I switch back to main window
        And I wait for the ajax response
        And I wait for 6 seconds
        Then I should see an ".btn-success .fa-linkedin" element

