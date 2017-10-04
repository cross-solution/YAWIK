@todo
Feature: Apply a job
    In order to start working
    As a user
    I should be able to apply a job

    Background:
        Given I have a admin with the following:
            | Login                       | test@admin.com        |
            | Fullname                    | Test Admin User       |
            | Password                    | test                  |
            | Organization                | Cross Solution        |
        And I have a published job with the following:
            | Title                       | Apply a Job Test      |
            | Professions                 | Sales,Law,HR          |
            | Industries                  | Banking,IT            |
            | Employment Types            | Permanent             |
            | Location                    | 10117 Berlin          |
            | Company Name                | Test Company          |
            | User                        | test@admin.com        |


    @javascript
    Scenario: Apply job as guest
        Given I apply for "Apply a Job Test" job
        When I click edit on "Personal Informations" form
        And I wait for the ajax response
        And I fill in the following:
            | First name        | Guest             |
            | Last name         | Applicant         |
            | street            | Some Street       |
            | house number      | 123456            |
            | Postalcode        | 4321              |
            | City              | Some City         |
            | Phone             | 654321            |
            | Email             | guest@apply.com   |
        And I select "Mr." from "Salutation"
        And I scroll "#send-application-buttons" into view
        And check "I have read the Privacy Policy and accept it"
        And I wait for 5 seconds
        And I press "Save"
        And I wait for the ajax response
        Then I should see "Guest Applicant"
        And I should see "Some Street 123456"
        And I should see "4321 Some City"
        And I should see "654321"
        And I should see "guest@apply.com"

    @javascript
    Scenario: Apply job as registered user
        Given I have a user with the following:
            | Login                       | applicant@user.com    |
            | Fullname                    | Test Applicant        |
            | Password                    | test                  |
        And I am logged in as "applicant@user.com" identified by "test"
        And I apply for "Apply a Job Test" job
        When I click edit on "Personal Informations" form
        And I wait for the ajax response
        And I fill in the following:
            | First name        | Test              |
            | Last name         | Applicant         |
            | street            | Some Street       |
            | house number      | 123456            |
            | Postalcode        | 4321              |
            | City              | Some City         |
            | Phone             | 654321            |
            | Email             | test@apply.com    |
        And I select "Mr." from "Salutation"
        And I scroll "#send-application-buttons" into view
        And check "I have read the Privacy Policy and accept it"
        And I press "Save"
        And I wait for the ajax response
        Then I should see "Test Applicant"
        And I should see "Some Street 123456"
        And I should see "4321 Some City"
        And I should see "654321"
        And I should see "test@apply.com"