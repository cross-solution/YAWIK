Feature: Resume Management
    In order to apply a job
    As a User
    I should be able to manage my resume


    Background:
        Given I have a user with the following:
            | Login                 | test@resume.com           |
            | Fullname              | Test Resume               |
            | Password              | test                      |
        And I am logged in as "test@resume.com" identified by "test"


    Scenario: Updating personal informations
        Given I go to manage my resume page
        Then I should see "Manage my resume"
        When I click edit on my personal information
        And I wait for 1 seconds
        And I fill in the following:
            | First name        | Test              |
            | Last name         | Resume            |
            | street            | Some Street       |
            | house number      | 123456            |
            | Postalcode        | 4321              |
            | City              | Some City         |
            | Phone             | 654321            |
            | Email             | test@resume.com   |
        And I select "Mr." from "Salutation"
        And I press "Save"
        And I wait for the ajax response
        And I wait for 2 seconds
        Then I should see "Test Resume"
        And I should see "Some Street 123456"
        And I should see "4321 Some City"
        And I should see "654321"
        And I should see "test@resume.com"

