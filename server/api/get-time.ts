import { PrismaClient, Prisma } from '@prisma/client'
import moment from 'moment';

export default defineEventHandler(async (event) => {
  const prisma = new PrismaClient()

  let time = await prisma.time.findFirst();

  if(time === null) {
    time = await prisma.time.create({
      data: {
        timestamp: '1738378800000',
        timeToUpdate: moment().hour(6).add(1, 'day').format('YYYY-MM-DD hh:mm:ss')
      }
    })
  } else if(moment().isAfter(time.timeToUpdate)) {
    time = await prisma.time.update({
      where: {
        id: time.id
      },
      data: {
        timestamp: '1738378800000',
        timeToUpdate: moment().add(1, 'day').format('YYYY-MM-DD hh:mm:ss')
      }
    })
  }

  return time
})
