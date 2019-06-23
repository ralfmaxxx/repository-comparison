Feature:
    In order to get some repository statistics
    As a API Client
    I want to get comparison details

    Scenario: It returns 404 when comparison was not found
        When I send "GET" request to "/api/v1/comparison/xyz"
        Then the response status should be 404

    Scenario: It returns 200 when comparison was found
        Given there are repository statistics:
            | id                                   | username | name | status    | forks_count | stars_count | watchers_count | last_release_date   | open_pr_count | closed_pr_count |
            | 3a2779eb-5cc4-42bd-b111-9eb32b0cf2e7 | doctrine | orm  | delivered | 23          | 11          | 3              | 2019-01-01 12:00:00 | 2             | 33              |
            | c29cdacd-c216-4ba6-a7f1-0e5e6a79fb48 | doctrine | dbal | delivered | 44          | 55          | 6              | 2018-01-01 12:00:00 | 6             | 66              |
        And there are comparisons:
            | id                                   | first_statistics_id                  | second_statistics_id                 |
            | c29cdacd-c216-4ba6-a7f1-0e5e6a79fb48 | 3a2779eb-5cc4-42bd-b111-9eb32b0cf2e7 | c29cdacd-c216-4ba6-a7f1-0e5e6a79fb48 |
        When I send "GET" request to "/api/v1/comparison/c29cdacd-c216-4ba6-a7f1-0e5e6a79fb48"
        Then the response status should be 200
        And the response should be:
        """
            {
                "id": "c29cdacd-c216-4ba6-a7f1-0e5e6a79fb48",
                "firstRepository": {
                    "id": "3a2779eb-5cc4-42bd-b111-9eb32b0cf2e7",
                    "name": "doctrine/orm",
                    "status": "delivered",
                    "starsCount": 11,
                    "forksCount": 23,
                    "watchersCount": 3,
                    "lastReleaseDate": "2019-01-01 12:00:00",
                    "openPRCount": 2,
                    "closedPRCount": 33
                },
                "secondRepository": {
                    "id": "c29cdacd-c216-4ba6-a7f1-0e5e6a79fb48",
                    "name": "doctrine/dbal",
                    "status": "delivered",
                    "starsCount": 55,
                    "forksCount": 44,
                    "watchersCount": 6,
                    "lastReleaseDate": "2018-01-01 12:00:00",
                    "openPRCount": 6,
                    "closedPRCount": 66
                }
            }
        """
