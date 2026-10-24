FROM node:24-bookworm-slim

#set working directory
WORKDIR /app

#copy package.json and lock first
COPY package*.json ./

# Clean install
RUN npm ci


COPY . .

EXPOSE 5173

CMD ["npm", "run", "dev"]
