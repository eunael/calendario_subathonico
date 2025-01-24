import { PrismaClient } from '@prisma/client';
import moment from 'moment';
import 'moment/locale/pt-br'

export default defineEventHandler(async (event) => {
  const runtimeConfig = useRuntimeConfig()
  const prisma = new PrismaClient()

  const time = await prisma.time.findFirst()

  if(time == null) {
    const data: object = await $fetch(runtimeConfig.secretEndpoint)
    const timeLeft = data?.timeLeft
    const currentTime = Number.parseInt(moment().locale('pt-br').format('x'))

    const finalTime = currentTime + timeLeft

    return await prisma.time.create({
      data: {
        timestamp: `${finalTime}`,
        timeToUpdate: moment().locale('pt-br').hour(6).add(1, 'days').format('YYYY-MM-DD hh:mm:ss')
      }
    })
  } else if (moment().locale('pt-br').isAfter(time.timeToUpdate)) {
    const data: object = await $fetch(runtimeConfig.secretEndpoint)
    const timeLeft = data?.timeLeft
    const currentTime = Number.parseInt(moment().locale('pt-br').format('x'))

    const finalTime = currentTime + timeLeft

    return await prisma.time.update({
      where: {
        id: time.id
      },
      data: {
        timestamp: `${finalTime}`,
        timeToUpdate: moment().locale('pt-br').hour(6).add(1, 'days').format('YYYY-MM-DD hh:mm:ss')
      }
    })
  }

  return time
})
