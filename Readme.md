# User Authorization

> Kanata Plugin

This plugin adds an authorization layer where it is configurable which routes will be protected, and a formal way of keeping Users.

## Installation

Activate plugin:

```shell
php kanata plugin:activate UserAuthorization
```

Publish config file:

```shell
php kanata plugin:publish UserAuthorization config
```

## Config

### Seed

To start with your first user without having to register through the UI, you can seed via kanata command:

```shell
php kanata user-auth:seed --name=John --email=johngalt@example.com --password=secret --email-verified
```

### Email Verification

To have the email verified, you'll need 2 items to be in place:

1. [Mail Plugin](https://github.com/kanata-php/mail-plugin) installed and active.
2. Configuration `authorization.email-confirmation` set to `true`.

Once you have those 2 in place, the system will send an email for confirmation. Without that users will have email set to verified as soon as they register.

## API

### Single-Use Token

this endpoint servers the purpose to generate a Token to be used only once, after that it gets expired.

#### Request

`GET /api/issue-single-use-token`

#### Responses

**Success:**

Status: 200

Header:
```
Authorization: Bearer {token-value}
```

Body:
```json
{
  "token": "token-value"
}
```

**Errors**

422 - Validation/Authorization Error.

500 - Server Error.

## Commands

### Issue token

```shell
php kanata token:issue --name="my-token-name" --email=doe@kanata.com
```

### Seed Users

```shell
php kanata user-auth:seed --name="John Doe" --email=doe@kanata.com --password=secret
```
