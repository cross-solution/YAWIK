Feature: Updating my organization
    In order to post a job
    As a recruiter
    I should able to update my organization

    Background:
        Given I am logged in as a recruiter
        And I go to my organization page

    @organization
    Scenario: Updating Name
        When I click edit on name form
        And I wait for the ajax response
        And I fill in "Organizationname" with "Some Organization"
        And I press "Save"
        And I wait for the ajax response
        Then I should see "Some Organization"

    @organization
    Scenario: Updating Location
        When I click edit on location form
        And I wait for the ajax response
        And I fill in the following:
            | street        | Some Street   |
            | house number  | 12345         |
            | Postalcode    | 54321         |
            | City          | Some City     |
            | Phone         | 123123        |
            | Fax           | 321321        |
        And I save "organization location" form
        And I wait for the ajax response
        Then I should see "Some Street"
        And I should see "12345"
        And I should see "54321"
        And I should see "Some City"
        And I should see "123123"
        And I should see "321321"

    @organization @mail
    Scenario: Invite employee
        When I click edit on name form
        And I wait for the ajax response
        And I fill in "Organizationname" with "Some Organization"
        And I press "Save"
        And I wait for the ajax response
        And I click edit on employees form
        And I wait for the ajax response
        And I fill in "Via email address" with "test.invite@example.com"
        And I press "Invite"
        And I wait for the ajax response
        And I wait for 5 seconds
        Then I should see "test.invite@example.com"
        And an email should be sent to "test.invite@example.com"
        And an email should be sent from "email@example.com"
        And sent email should be contain "Test Recruiter invited you"

    @organization
    Scenario: Setup Workflow
        When I click edit on workflow form
        And I uncheck "accept Applications by Department Managers"
        And I uncheck "assign department managers to jobs"
        And I wait for the ajax response
        And I scroll "#sf-workflowSettings" into view
        And I save workflow form
        And I wait for the ajax response
        Then the "accept Applications by Department Managers" checkbox should not be checked
        And the "assign department managers to jobs" checkbox should not be checked
