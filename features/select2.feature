Feature: Update job
    In order to get an applicant
    As a recruiter
    I should be able to manage my job posts

    @javascript @job-create
    Scenario: Test Select2
        Given I go to job board page
        When I fill in select2 search "l" with "Berlin" and I choose "10117 Berlin"
        And I wait for the ajax response
        And I wait for 10 seconds
        #And I save "job title and location" form
        #And I wait for the ajax response
        #Then I should see "Test Job"
