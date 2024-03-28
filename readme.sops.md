## sops configuration
The application container comes equipped with sops, age, and age-keygen, streamlining the process of managing sensitive information.

#### Key management
* decryption requires a secret key, specified in the `.env` file
* encryption utilizes a public key, detailed within the `.sops.yaml` file
* it's essential to note that the age public and secret keys are interdependent

### Beta environment
For operations within the beta environment assign the `SOPS_AGE_BETA_SECRET_KEY` in the `.env` file for decryption purposes.

#### Decrypting beta secrets
Execute the following command to decrypt the `.env.beta.secrets` file, generating or updating the `.env.beta.secrets.decrypted` file:
```shell
make decrypt-beta-secrets
```

#### Encrypting beta secrets
To encrypt the `.env.beta.secrets.decrypted` file, thus creating or updating the `.env.beta.secrets` file, use:
```shell
make encrypt-beta-secrets
```

Files are stored in `./environment/prod/deployment/beta`

### Production environment
For operations within the beta environment assign the `SOPS_AGE_PROD_SECRET_KEY` in the `.env` file for decryption purposes.

#### Decrypting production secrets
Execute the following command to decrypt the `.env.prod.secrets` file, generating or updating the `.env.prod.secrets.decrypted` file:
```shell
make decrypt-prod-secrets
```

#### Encrypting production secrets
To encrypt the `.env.prod.secrets.decrypted` file, thus creating or updating the `.env.prod.secrets` file, use:
```shell
make encrypt-prod-secrets
```

Files are stored in `./environment/prod/deployment/prod`
