import { int, sqliteTable, text } from "drizzle-orm/sqlite-core";

export const timeTable = sqliteTable("time", {
  id: int().primaryKey({ autoIncrement: true }),
  timestamp: text().notNull(),
  timeToUpdate: text().notNull(),
});