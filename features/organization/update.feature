@organization
Feature: Updating my organization
    In order to post a job
    As a recruiter
    I should able to update my organization

    Background:
        Given I am logged in as a recruiter
        And I go to my organization page

    Scenario: Updating Name
        When I click edit on name form
        And I wait for the ajax response
        And I fill in "Organization Name" with "Some Organization"
        And I press "Save"
        And I wait for the ajax response
        Then I should see "Some Organization"

    Scenario: Updating Location
        When I click edit on location form
        And I wait for the ajax response
        And I fill in the following:
            | Street        | Some Street   |
            | House Number  | 12345         |
            | Postal Code   | 54321         |
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

    Scenario: Invite employee
        When I click edit on name form
        And I wait for the ajax response
        And I fill in "Organization Name" with "Some Organization"
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

    Scenario: Add and remove logo
        When I want to edit my organization
        And I attach logo from file "img/logo.jpg"
        And I wait for 2 seconds
        Then I should see an "img.img-polaroid" element
        # test removing a logo
        When I remove logo from organization
        And I wait for 5 seconds
        And I want to edit my organization
        Then the "h1" element should contain "Organization"
        # @ todo fix this error below
        #And I wait for 2 seconds
        #And I should not see an "img.img-polaroid" element
