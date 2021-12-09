## Message Queue worker

- Create Wallet

Message In:

```json
{
  "operation": "create_wallet",
  "tags": {
    "app_id": "sdf",
    "action_id": 20,
    "user_id": "my-account.testnet"
  },
  "args": {
    "new_account_id": "my-account.testnet",
  }
}
```

Message Out:

```json
{
  "operation": "create_wallet_out",
  "success": true,
  "args": {
    "new_account_id": "my-account.testnet"
  }
}
```

- Create NFT Series

Message In:

```json
{
  "operation": "create_nft_series",
  "tags": {
    "app_id": "sdf",
    "action_id": 21,
    "user_id": "my-friend.testnet"
  },
  "args": {
    "creator_id": "my-account.testnet",
    "token_metadata": {
      "title": "Title of my NFT",
      "media": "https://ipfs.io/ipfs/bafybeicvjdjdxhu6oglore3dw26pclogws2adk7gtmsllje6siinqq4uzy",
      "reference": "https://ipfs.io/ipfs/bafybeigo6bjoq6t5dl4fqgvwosplvbkbu5ri6wo3cmkxmypi4sj2j2ae54",
      "copies": 20
    }
  }
}
```

Message Out:

```json
{
  "operation": "create_nft_series_out",
  "success": true,
  "args": {
    "token_id": 1234
  }
}
```

- Transfer NFT

Message In:

```json
{
  "operation": "transfer_nft",
  "tags": {
    "app_id": "sdf",
    "action_id": 22,
    "user_id": "my-account.testnet",
  },
  "args": {
    "token_id": "1",
    "sender_id": "my-account.testnet",
    "receiver_id": "my-friend.testnet"
  }
}
```

Message Out:

```json
{
  "operation": "transfer_nft_out",
  "success": true
}
```
