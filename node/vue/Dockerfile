FROM node:20 AS builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM node:20

WORKDIR /app
ENV NODE_ENV=production \
    REACTIVE_ASSET_NAMESPACE=vue

COPY --from=builder /app/package*.json ./
COPY --from=builder /app/node_modules ./node_modules
COPY --from=builder /app/dist ./dist
COPY --from=builder /app/server.js ./server.js
COPY --from=builder /app/vite.config.js ./vite.config.js
COPY --from=builder /app/entry-client.js ./entry-client.js
COPY --from=builder /app/entry-server.js ./entry-server.js
COPY --from=builder /app/createApp.js ./createApp.js
COPY --from=builder /app/App.vue ./App.vue
COPY --from=builder /app/pages ./pages
COPY --from=builder /app/components ./components
COPY docker-entrypoint.sh ./docker-entrypoint.sh

RUN chmod +x ./docker-entrypoint.sh

EXPOSE 5173

ENTRYPOINT ["./docker-entrypoint.sh"]
CMD ["node", "server.js"]

