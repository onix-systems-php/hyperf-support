# onix-systems-php/hyperf-support

**Hyperf-support** is a package for fluently managing your tickets and comments within Slack, Trello and other systems. Made by [onix-systems-php](https://github.com/onix-systems-php)

## Installation:
```shell
composer require onix-systems-php/hyperf-support
```
## Publishing the config:
```shell
php bin/hyperf.php vendor:publish onix-systems-php/hyperf-support
```
## Configuration

### Configure `app`
1. `domain` - application url e.g.(https://github.com).
2. `name` - application name.
3. `team_name` - name of your support team.
4. `user_model_class` - User model path. Then implement `OnixSystemsPHP\HyperfSupport\Contract\SupportUserInterface` contract in the model class.

### Configure `integrations.trello`
1. `key` - API Key. (You may find it here: https://trello.com/power-ups/admin)
2. `token` - Authorization token.
3. `webhook_url` - `app.domain` + `/v1/support/webhooks/trello`.
4. `board_name` - Trello board name.
5. `members` - For each type of ticket specify members which should be attached to the card on Trello.
6. `lists` - Specify mapping for each status of ticket with corresponding list on Trello.
7. `custom_fields` - Determine which custom fields should be on card on Trello.
8. `trigger_lists` - Specify trigger lists on Trello. These lists determine whether to notify users if the ticket moved in one of these lists.
9. `is_private_discussion` - This option must be `true` or `false`. If `true`, discussion under the ticket on Trello will be private and anyone can see it except on Trello.
10. `keys_to_source` - Specify `your_api_username` => `your_source`.

### Configure `integrations.slack`
1. `token` - Bot Authorization key.
2. `channel_id` - Slack channel id.
3. Don't forget to enable subscriptions for your Slack bot and specify request URL: `app.domain` + `/v1/support/webhooks/trello`.
4. `app_icon` - Your application's icon url. e.g.
5. `members` - For each type of ticket specify members which should be mentioned on Slack ticket. **Without '@'.**
6. `custom_fields` - Determine which custom fields should be showed on Slack ticket.
7. `is_private_discussion` - This option must be `true` or `false`. If `true`, discussion under the ticket on Slack will be private and anyone can see it except on Slack.
8. `keys_to_source` - Specify `your_slack_channel_id` => `your_source`.

### Configure `routes`
`require_once './vendor/onix-systems-php/hyperf-support/publish/routes.php';`

## Basic Usage

### Creating simple ticket:
Try to send this `JSON` to `/v1/support/tickets` via `POST` method.
```json
{
  "source": "default",
  "title": "Lorem ipsum.",
  "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.",
  "custom_fields": {
    "type": "Tweak",
    "level": 3,
    "priority": "Medium",
    "status": "New"
  },
  "page_url": "https://google.com"
}
```
You should get something like this object:
```json
{
    "id": 1,
    "title": "Lorem ipsum.",
    "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.",
    "source": "default",
    "custom_fields": {
        "type": "Tweak",
        "level": 3,
        "status": "New",
        "priority": "Medium"
    },
    "created_by": 6,
    "modified_by": null,
    "deleted_by": null,
    "completed_at": null,
    "trello_id": "660bc45ce19c204556caf1f5",
    "trello_short_link": "qTHPdFoL",
    "slack_id": "1712047194.779679",
    "page_url": null,
    "created_at": "2024-04-02 08:39:54",
    "updated_at": "2024-04-02 08:40:36",
    "deleted_at": null,
    "files": []
}
```
Finally, it should appear on Slack and Trello.

### Creating ticket with files:
Logic the same as for creating simple ticket, but you need to pass array with files' IDs:
```json
{
    ...
    "files": [1, 2, 3]
}
```
Finally, the ticket should appear on Slack and Trello with attached files.

### Updating ticket on Trello.
Once the `ticket.done_status` is "Done" everytime when ticket moved to Done list on Trello the ticket will be marked as "completed".
