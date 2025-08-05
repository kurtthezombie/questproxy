FROM node:18

#set working directory
WORKDIR /app

#copy package.json and lock first
COPY package*.json ./

# Clean install
RUN npm install --legacy-peer-deps


COPY . .

EXPOSE 5173

CMD ["npm", "run", "dev"]