Feature: Update job
    In order to get an applicant
    As a recruiter
    I should be able to manage my job posts

    Background:
        Given I am logged in as a recruiter with "Cross Solution" as organization

    @javascript @job-create
    Scenario: Successfully create new job
        Given I don't have any posted job
        And I go to create job page
        And I wait for the ajax response
        When I fill job location search with "Berlin" and choose "10117 Berlin"
        And I fill in "Job title" with "Test Job Draft"
        And I save "job title and location" form
        And I wait for the ajax response
        Then I should see "Test Job Draft"
        When I click edit on "job classification" form
        And I choose "Sales" from professions
        And I choose "Banking" from industries
        And I fill in select2 "#classifications-employmentTypes-span .select2-container" with "Contract"
        And I save "job classification" form
        And I wait for the ajax response
        Then I should see "Sales"
        And I should see "Banking"
        And I should see "Contract"
        When I click edit on "customer note" form
        And I wait for the ajax response
        And I fill in "customerNote-note" with "Some Note"
        And I save "customer note" form
        And I wait for the ajax response
        Then I should see "Some Note"


    @javascript
    Scenario: Successfully edit title and job location
        Given I go to edit job draft with title "Test Job Draft"
        And I follow "Basic Data"
        When I fill in "Job title" with "Edited Test Job Draft"
        And I fill job location search with "Berlin" and choose "10117 Berlin"
        When I save "job title and location" form
        And I wait for the ajax response
        Then I should see "Edited Test Job Draft"

    @javascript
    Scenario: Successfully edit classifications
        Given I don't have any posted job
        And I go to edit job draft with title "Test Job Draft"
        And I click edit on "job classification" form
        And I choose "Sales" from professions
        And I choose "Banking" from industries
        And I choose "Permanent" from "employment types"
        And I save "job classification" form
        And I wait for the ajax response
        Then I should see "Sales"
        And I should see "Banking"
        And I should see "Permanent"

    @javascript
    Scenario: Successfully edit customer note
        Given I don't have any posted job
        And I go to edit job draft with title "Test Job Draft"
        And I click edit on "customer note" form
        And I fill in "customerNote-note" with "Edited Some Note"
        And I save "customer note" form
        And I wait for the ajax response
        Then I should see "Edited Some Note"



