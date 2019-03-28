FROM node:8-alpine

RUN npm install -g serverless --ignore-scripts spawn-sync

RUN npm -v
RUN node -v
RUN sls -v