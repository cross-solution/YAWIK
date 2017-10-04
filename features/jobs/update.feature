Feature: Update job
    In order to get an applicant
    As a recruiter
    I should be able to manage my job posts

    Background:
        Given I have a recruiter with the following:
            | Login             | test@job.com                  |
            | Fullname          | Test Update Job               |
            | Password          | test                          |
            | Organization      | Cross Solution                |
        And I have a draft job with the following:
            | Title                       | Test Job Draft        |
            | User                        | test@job.com          |
        And I am logged in as "test@job.com" identified by "test"

    @job-create
    Scenario: Successfully create new job
        Given I don't have any posted job
        And I go to create job page
        And I wait for the ajax response
        When I fill job location search with "Berlin" and choose "10117 Berlin"
        And I fill in "Job title" with "Test Create Job"
        And I save "job title and location" form
        And I wait for the ajax response
        Then I should see "Test Create Job"
        When I click edit on "job classification" form
        And I wait for the ajax response
        And I scroll "#general-classifications-buttons-submit" into view
        And I choose "Sales" from professions
        And I choose "Banking" from industries
        And I fill in select2 "#classifications-employmentTypes-span .select2-container" with "Contract"
        And I save "job classification" form
        And I wait for the ajax response
        Then I should see "Sales"
        And I should see "Banking"
        And I should see "Contract"

    Scenario: Successfully edit classifications
        Given I go to edit job draft with title "Test Job Draft"
        And I click edit on "job classification" form
        And I wait for the ajax response
        And I choose "Sales" from professions
        And I choose "Banking" from industries
        And I choose "Permanent" from "employment types"
        And I save "job classification" form
        And I wait for the ajax response
        Then I should see "Sales"
        And I should see "Banking"
        And I should see "Permanent"

    Scenario: Successfully edit customer note
        Given I go to edit job draft with title "Test Job Draft"
        And I scroll "#job_incomplete" into view
        And I click edit on "customer note" form
        And I wait for the ajax response
        And I fill in "customerNote-note" with "Edited Some Note"
        And I save "customer note" form
        And I wait for the ajax response
        Then I should see "Edited Some Note"

    Scenario: Successfully edit title and job location
        Given I go to edit job draft with title "Test Job Draft"
        When I fill in "Job title" with "Edited Test Job Draft"
        And I fill job location search with "Berlin" and choose "10117 Berlin"
        When I save "job title and location" form
        And I wait for the ajax response
        Then I should see "Edited Test Job Draft"
