Feature: Publish Job
    In order to get applicants
    As recruiter
    I should able to publish my job

    Background:
        Given I am logged in as a recruiter with "Cross Solution" as organization
        And I don't have any posted job
        And I have a draft job with the following:
            | Title                       | Test Publishing a Job |
            | Professions                 | Sales,Law,HR          |
            | Industries                  | Banking,IT            |
            | Employment Types            | Permanent             |
            | Location                    | 10117 Berlin          |
            | Company Name                | Test Company          |

    @javascript
    Scenario: Successfully publish a job
        Given I go to edit job draft with title "Test Publish a Job"
        And I follow "Publish job"
        Then I should see "Publishing successfully finished"