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

    @javascript @skip-travis
    Scenario: Successfully publish a job
        Given I go to edit job draft with title "Test Publishing a Job"
        And I wait for the ajax response
        And I scroll "#yk-footer" into view
        And I follow "Publish job"
        Then I should see "Publishing successfully finished"