FROM node:20 AS builder

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

FROM node:20

WORKDIR /app
ENV NODE_ENV=production \
    REACTIVE_ASSET_NAMESPACE=next

COPY --from=builder /app/package*.json ./
COPY --from=builder /app/node_modules ./node_modules
COPY --from=builder /app/.next ./.next
COPY --from=builder /app/server.js ./server.js
COPY --from=builder /app/next.config.mjs ./next.config.mjs
COPY --from=builder /app/components ./components
COPY --from=builder /app/pages ./pages
COPY --from=builder /app/styles ./styles
COPY docker-entrypoint.sh ./docker-entrypoint.sh

RUN chmod +x ./docker-entrypoint.sh

EXPOSE 5175

ENTRYPOINT ["./docker-entrypoint.sh"]
CMD ["node", "server.js"]

