import { eq } from 'drizzle-orm';
import { drizzle } from 'drizzle-orm/libsql';
import moment from 'moment';
import 'moment/locale/pt-br'
import { timeTable } from '~/src/db/schema';

const runtimeConfig = useRuntimeConfig()

const db = drizzle(process.env.DB_FILE_NAME!);

export default defineEventHandler(async (event) => {

  const timesSelection = await db.select().from(timeTable)
  const time = timesSelection[0]

  if(!time) {
    const data: object = await $fetch(runtimeConfig.secretEndpoint)
    const timeLeft = data?.timeLeft
    const currentTime = Number.parseInt(moment().locale('pt-br').format('x'))

    const finalTime = currentTime + timeLeft

    return await db.insert(timeTable).values({
      timestamp: `${finalTime}`,
      timeToUpdate: moment().locale('pt-br').hour(6).add(1, 'days').format('YYYY-MM-DD hh:mm:ss')
    }).returning()
  } else if (moment().locale('pt-br').isAfter(time.timeToUpdate)) {
    const data: object = await $fetch(runtimeConfig.secretEndpoint)
    const timeLeft = data?.timeLeft
    const currentTime = Number.parseInt(moment().locale('pt-br').format('x'))

    const finalTime = currentTime + timeLeft

    return await db.update(timeTable)
      .set({
        timestamp: `${finalTime}`,
        timeToUpdate: moment().locale('pt-br').hour(6).add(1, 'days').format('YYYY-MM-DD hh:mm:ss')
      })
      .where(eq(timeTable.id, time.id))
      .returning()
  }

  return time
})
