Feature: Updating my organization
    In order to post a job
    As a recruiter
    I should able to update my organization

    Background:
        Given I am logged in as a recruiter
        And I go to my organization page

    @javascript @organization-name
    Scenario: Updating Name
        When I click edit on name form
        And I press "Edit"
        And I fill in "Organizationname" with "Some Organization"
        And I save name form
        And I wait for the ajax response
        Then I should see "Some Organization"

    @javascript @organization-location
    Scenario: Updating Location
        When I click edit on location form
        And I fill in the following:
            | street        | Some Street   |
            | house number  | 12345         |
            | Postalcode    | 54321         |
            | City          | Some City     |
            | Phone         | 123123        |
            | Fax           | 321321        |
        And I save location form
        And I wait for the ajax response
        Then I should see "Some Street"
        And I should see "12345"
        And I should see "54321"
        And I should see "Some City"
        And I should see "123123"
        And I should see "321321"

    @javascript @organization-employee
    Scenario: Invite employee
        When I click edit on name form
        And I press "Edit"
        And I fill in "Organizationname" with "Some Organization"
        And I save name form
        And I wait for the ajax response
        And I click edit on employees form
        And I wait for the ajax response
        And I fill in "Via email address" with "test.yawik@gmail.com"
        And I press "Invite"
        And I wait for the ajax response
        And I wait for 5 seconds
        Then I should see "test.yawik@gmail.com"
