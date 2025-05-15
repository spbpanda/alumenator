#!/bin/bash

zip -r theme.zip ./frontend -x "./frontend/node_modules/*" -x "./frontend/.next/*" -x "./pnpm-lock.yaml"