Feature: Publish Job
    In order to get applicants
    As recruiter
    I should able to publish my job

    Background:
        Given I have a recruiter with the following:
            | Login                       | test@recruiter.com    |
            | Fullname                    | Test Recruiter        |
            | Password                    | test                  |
            | Organization                | Cross Solution        |
        And I have a draft job with the following:
            | Title                       | Test Publishing a Job |
            | Professions                 | Sales,Law,HR          |
            | Industries                  | Banking,IT            |
            | Employment Types            | Permanent             |
            | Location                    | 10117 Berlin          |
            | Company Name                | Test Company          |
            | User                        | test@recruiter.com    |
        And I am logged in as "test@recruiter.com" identified by "test"

    # disabled because we can not test email feature on travis
    @javascript @mail
    Scenario: Successfully publish a job
        Given I go to edit job draft with title "Test Publishing a Job"
        And I wait for the ajax response
        And I scroll "#yk-footer" into view
        And I follow "Create job opening"
        And I wait for the ajax response
        And I follow "Preview"
        And I wait for the ajax response
        And I scroll "#job_incomplete" into view
        And I check "I have read the terms an conditions and accept it"
        And I wait for the ajax response
        And I scroll "#job_incomplete" into view
        And I follow "Publish job"
        And I wait for the ajax response
        Then I should see "Publishing successfully finished"
