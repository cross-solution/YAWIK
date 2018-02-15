Feature: Add new organization
    In order to post a job
    As an admin
    I should able to add new organization

    @javascript @organization
    Scenario: Create organization when main organization not exists
        Given I don't have "test@recruiter.com" user
        When I am logged in as a recruiter
        And I go to create new organization page
        Then I should see "Sorry, you may not create organizations."
        And I should see "Before you are able to create organizations, you have to create your main organization first."

    @javascript @organization
    Scenario: Create organization successfully
        Given I am logged in as a recruiter with "Cross Solution" as organization
        And I go to create new organization page
        And I click edit on name form
        And I wait for the ajax response
        And I fill in "Organization Name" with "Some Organization"
        And I save "organization name" form
        And I wait for the ajax response
        Then I should see "Some Organization"

        # updating location
        When I click edit on location form
        And I fill in the following:
            | Street        | Some Street   |
            | House Number  | 12345         |
            | Postal Code   | 54321         |
            | City          | Some City     |
            | Phone         | 123123        |
            | Fax           | 321321        |
        And I scroll "#form-organizationLogo" into view
        And I save "organization location" form
        And I wait for the ajax response
        Then I should see "Some Street"
        And I should see "12345"
        And I should see "54321"
        And I should see "Some City"
        And I should see "123123"
        And I should see "321321"

        # upload organization logo
        When I attach the file "img/logo.jpg" to "organizationLogo-original"
        And I wait for the ajax response
        Then I should see an "img.img-polaroid" element
